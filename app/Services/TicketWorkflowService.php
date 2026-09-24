<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Notifications\ProjectCreatedNotification;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use Illuminate\Support\Facades\Notification;

class TicketWorkflowService
{
    public function statusLabel(string $status): string
    {
        return match ($status) {
            'open' => 'Menunggu tindak lanjut',
            'pending' => 'Sedang dikerjakan',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
            default => $status,
        };
    }

    /**
     * Set PIC tiket dari manager proyek, atau assignee tugas pertama.
     */
    public function syncTicketTechnicianFromProject(Project $project): void
    {
        if (!$project->ticket_id) {
            return;
        }

        $ticket = $project->ticket;
        if (!$ticket) {
            return;
        }

        $project->loadMissing(['manager', 'tasks']);

        $technicianId = $project->manager_id
            ?? $project->tasks->whereNotNull('assignee_id')->first()?->assignee_id;

        if (!$technicianId || $ticket->technician_id === $technicianId) {
            return;
        }

        $updates = ['technician_id' => $technicianId];
        if (!$ticket->first_response_at) {
            $updates['first_response_at'] = now();
        }

        $ticket->update($updates);
    }

    public function markTicketInProgressFromProject(Project $project): void
    {
        if (!$project->ticket_id) {
            return;
        }

        $ticket = $project->ticket;
        if (!$ticket || $ticket->status !== 'open') {
            return;
        }

        $ticket->update(['status' => 'pending']);
    }

    public function notifyProjectCreated(Project $project): void
    {
        $project->loadMissing(['client', 'manager', 'ticket']);

        $recipients = collect();

        if ($project->client) {
            $recipients->push($project->client);
        }

        if ($project->manager && !$recipients->contains(fn ($user) => $user->id === $project->manager_id)) {
            $recipients->push($project->manager);
        }

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new ProjectCreatedNotification($project));
        }
    }

    public function notifyTaskAssigned(Task $task, bool $reassigned = false): void
    {
        $task->loadMissing(['project.client', 'project.ticket', 'project.manager', 'assignee']);

        if ($task->assignee) {
            Notification::send(
                $task->assignee,
                new TaskAssignedNotification($task, $reassigned)
            );
        }

        if (!$task->project) {
            return;
        }

        $ticket = $task->project->ticket;
        $previousTechnicianId = $ticket?->technician_id;

        $this->syncTicketTechnicianFromProject($task->project);

        if ($ticket && $ticket->fresh()->technician_id !== $previousTechnicianId) {
            $this->notifyTicketStatusChange(
                $ticket->fresh(),
                'Penanggung jawab tiket Anda: ' . ($ticket->fresh()->technician?->name ?? '—')
            );
        }
    }

    public function notifyTicketStatusChange(Ticket $ticket, ?string $customMessage = null): void
    {
        $ticket->loadMissing(['client', 'technician', 'project']);

        $recipients = collect();

        if ($ticket->client) {
            $recipients->push($ticket->client);
        }

        if ($ticket->technician && !$recipients->contains(fn ($user) => $user->id === $ticket->technician_id)) {
            $recipients->push($ticket->technician);
        }

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new TicketStatusUpdatedNotification($ticket, $customMessage)
        );

        // Send WhatsApp notification to Client if phone number is present
        if ($ticket->client && !empty($ticket->client->phone)) {
            try {
                $statusLabel = $customMessage ?: $this->statusLabel($ticket->status);
                $waMessage = \App\Services\WhatsApp\PortalWhatsAppTemplates::ticketStatusUpdatedForClient(
                    ticket: $ticket,
                    clientName: $ticket->client->name,
                    statusLabel: $statusLabel
                );
                app(\App\Services\WhatsApp\WhatsAppService::class)->sendWhatsApp($ticket->client->phone, $waMessage);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Portal WA Ticket Notification Error: " . $e->getMessage());
            }
        }
    }

    public function notifyManagerChanged(Project $project, ?int $previousManagerId): void
    {
        $project->loadMissing(['manager', 'ticket']);

        $this->syncTicketTechnicianFromProject($project);

        if (!$project->ticket) {
            return;
        }

        if ($project->manager && $project->manager_id !== $previousManagerId) {
            Notification::send(
                $project->manager,
                new ProjectCreatedNotification($project, true)
            );
        }

        $this->notifyTicketStatusChange(
            $project->ticket->fresh(),
            'Penanggung jawab (PIC) proyek tiket Anda telah diperbarui.'
        );
    }

    public function afterLinkedTicketSynced(Ticket $ticket, ?string $previousStatus): void
    {
        if ($previousStatus === $ticket->status) {
            return;
        }

        $this->notifyTicketStatusChange($ticket);
    }
}

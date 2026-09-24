<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ProjectCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Project $project,
        public bool $managerAssignment = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $ticketTitle = $this->project->ticket?->title;

        if ($this->managerAssignment && $notifiable->id === $this->project->manager_id) {
            $message = 'Anda ditugaskan sebagai PIC proyek: ' . $this->project->name;
        } elseif (method_exists($notifiable, 'hasRole') && $notifiable->hasRole('client')) {
            $message = 'Tiket Anda sedang ditangani melalui proyek: ' . $this->project->name;
        } else {
            $message = 'Proyek baru dibuat: ' . $this->project->name;
        }

        return [
            'project_id' => $this->project->id,
            'ticket_id' => $this->project->ticket_id,
            'title' => $this->project->name,
            'ticket_title' => $ticketTitle,
            'url' => route('projects.show', $this->project),
            'message' => $message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}

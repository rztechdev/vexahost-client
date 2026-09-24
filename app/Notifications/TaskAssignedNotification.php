<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public bool $reassigned = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $projectName = $this->task->project?->name ?? 'Proyek';

        $message = $this->reassigned
            ? 'Penugasan tugas diperbarui: ' . $this->task->name . ' (' . $projectName . ')'
            : 'Tugas baru untuk Anda: ' . $this->task->name . ' (' . $projectName . ')';

        return [
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'ticket_id' => $this->task->project?->ticket_id,
            'title' => $this->task->name,
            'project_name' => $projectName,
            'url' => route('projects.show', $this->task->project_id),
            'message' => $message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}

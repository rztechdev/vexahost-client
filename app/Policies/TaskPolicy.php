<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole(['admin', 'ceo'])) return true;
        if ($user->hasRole('client')) return $task->project->client_id === $user->id;
        
        // Technician can view if assigned, or if they manage the project
        if ($user->hasRole('technician')) {
            return $task->assignee_id === $user->id 
                || $task->project->manager_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('tasks.manage');
    }

    public function update(User $user, Task $task): bool
    {
        return $user->can('tasks.manage');
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->can('tasks.manage');
    }
}

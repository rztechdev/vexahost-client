<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // all authenticated users can see project list
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole(['admin', 'ceo'])) return true;
        if ($user->hasRole('client')) return $project->client_id === $user->id;
        // technician: can view if they are a manager or have an assigned task
        if ($user->hasRole('technician')) {
            return $project->manager_id === $user->id
                || $project->tasks()->where('assignee_id', $user->id)->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('projects.manage');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('projects.manage');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('projects.manage');
    }
}

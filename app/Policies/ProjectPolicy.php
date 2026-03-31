<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;

class ProjectPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $user->workspaces()->whereKey($workspace->getKey())->exists();
    }

    public function view(User $user, Project $project): bool
    {
        return $user->workspaces()->whereKey($project->workspace_id)->exists();
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $user->workspaceRole($workspace) === 'ADMIN';
    }

    public function update(User $user, Project $project): bool
    {
        return $user->workspaceRole($project->workspace) === 'ADMIN';
    }

    public function archive(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}

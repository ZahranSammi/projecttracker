<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;

class IssuePolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $user->workspaces()->whereKey($project->workspace_id)->exists();
    }

    public function view(User $user, Issue $issue): bool
    {
        return $user->workspaces()->whereKey($issue->workspace_id)->exists();
    }

    public function create(User $user, Project $project): bool
    {
        return $user->workspaces()->whereKey($project->workspace_id)->exists();
    }

    public function update(User $user, Issue $issue): bool
    {
        return $user->workspaces()->whereKey($issue->workspace_id)->exists();
    }

    public function delete(User $user, Issue $issue): bool
    {
        return $user->workspaceRole($issue->workspace) === 'ADMIN'
            || $issue->reporter_id === $user->getKey();
    }

    public function assign(User $user, Issue $issue): bool
    {
        return $this->update($user, $issue);
    }

    public function moveStatus(User $user, Issue $issue): bool
    {
        return $this->update($user, $issue);
    }
}

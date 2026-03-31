<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    public function view(User $user, Workspace $workspace): bool
    {
        return $user->workspaces()->whereKey($workspace->getKey())->exists();
    }

    public function manage(User $user, Workspace $workspace): bool
    {
        return $user->workspaceRole($workspace) === 'ADMIN';
    }

    public function manageMembers(User $user, Workspace $workspace): bool
    {
        return $this->manage($user, $workspace);
    }

    public function manageWorkflow(User $user, Workspace $workspace): bool
    {
        return $this->manage($user, $workspace);
    }
}

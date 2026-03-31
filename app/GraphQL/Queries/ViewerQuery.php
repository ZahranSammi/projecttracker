<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

class ViewerQuery
{
    public function me(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user->loadMissing('memberships.user', 'workspaces') : null;
    }

    public function workspaceRole(Workspace $workspace): string
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->workspaceRole($workspace) ?? 'MEMBER';
    }
}

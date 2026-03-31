<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user, Issue $issue): bool
    {
        return $user->workspaces()->whereKey($issue->workspace_id)->exists();
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->author_id === $user->getKey()
            || $user->workspaceRole($comment->workspace) === 'ADMIN';
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $this->update($user, $comment);
    }
}

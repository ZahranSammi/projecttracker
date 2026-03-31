<?php

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\User;
use App\Services\IssueService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentMutation
{
    public function __construct(
        private readonly IssueService $issueService,
    ) {
    }

    public function addComment($_, array $args): Comment
    {
        /** @var User $user */
        $user = Auth::user();
        $issue = Issue::query()->findOrFail($args['input']['issueId']);
        Gate::authorize('create', [Comment::class, $issue]);

        return $this->issueService->addComment($user, $issue, $args['input']['body']);
    }
}

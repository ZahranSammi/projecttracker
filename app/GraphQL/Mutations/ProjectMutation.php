<?php

namespace App\GraphQL\Mutations;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProjectMutation
{
    public function __construct(
        private readonly ProjectService $projectService,
    ) {
    }

    public function createProject($_, array $args): Project
    {
        /** @var User $user */
        $user = Auth::user();
        $workspace = Workspace::query()->findOrFail($args['input']['workspaceId']);
        Gate::authorize('create', [Project::class, $workspace]);

        Validator::make($args['input'], [
            'workspaceId' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:12'],
            'description' => ['nullable', 'string'],
        ])->validate();

        return $this->projectService->create($user, $workspace, [
            'name' => $args['input']['name'],
            'key' => $args['input']['key'],
            'description' => $args['input']['description'] ?? null,
        ]);
    }

    public function updateProject($_, array $args): Project
    {
        /** @var User $user */
        $user = Auth::user();
        $project = Project::query()->findOrFail($args['id']);
        Gate::authorize('update', $project);

        return $this->projectService->update($user, $project, [
            'name' => $args['input']['name'] ?? null,
            'description' => $args['input']['description'] ?? null,
            'is_archived' => $args['input']['isArchived'] ?? null,
            'lead_user_id' => $args['input']['leadUserId'] ?? null,
        ]);
    }
}

<?php

namespace App\GraphQL\Queries;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\GraphQLConnection;
use Illuminate\Support\Facades\Gate;

class ActivityFeedQuery
{
    public function feed($_, array $args): array
    {
        $workspace = Workspace::query()->findOrFail($args['workspaceId']);
        Gate::authorize('view', $workspace);

        $builder = ActivityLog::query()
            ->with('actor')
            ->where('workspace_id', $workspace->getKey())
            ->when($args['projectId'] ?? null, fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->latest('created_at');

        return GraphQLConnection::fromBuilder($builder, $args['pagination'] ?? []);
    }
}

<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ActivityLogService
{
    public function record(Workspace $workspace, ?User $actor, string $action, Model $entity, array $metadata = []): ActivityLog
    {
        return ActivityLog::query()->create([
            'workspace_id' => $workspace->getKey(),
            'project_id' => $entity instanceof Project ? $entity->getKey() : ($entity instanceof Issue ? $entity->project_id : ($entity instanceof Comment ? $entity->issue?->project_id : null)),
            'issue_id' => $entity instanceof Issue ? $entity->getKey() : ($entity instanceof Comment ? $entity->issue_id : null),
            'comment_id' => $entity instanceof Comment ? $entity->getKey() : null,
            'actor_id' => $actor?->getKey(),
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->getKey(),
            'action' => $action,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    public function feed(Workspace $workspace, ?Project $project = null, int $limit = 20): Collection
    {
        $query = ActivityLog::query()
            ->with(['actor'])
            ->where('workspace_id', $workspace->getKey())
            ->latest('created_at');

        if ($project instanceof Project) {
            $query->where('project_id', $project->getKey());
        }

        return $query->limit($limit)->get();
    }
}

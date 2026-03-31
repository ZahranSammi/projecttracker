<?php

namespace App\Services;

use App\Models\IssueStatus;
use App\Models\Label;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkspaceService
{
    public function currentForUser(User $user): Workspace
    {
        $workspace = $user->currentWorkspace();

        if (! $workspace instanceof Workspace) {
            throw new AuthorizationException('No workspace is available for the current user.');
        }

        return $workspace->loadMissing(['memberships.user', 'labels', 'statuses']);
    }

    public function membershipFor(User $user, Workspace $workspace): ?TeamMember
    {
        return $workspace->memberships()
            ->where('user_id', $user->getKey())
            ->first();
    }

    public function roleFor(User $user, Workspace $workspace): ?string
    {
        return $this->membershipFor($user, $workspace)?->role;
    }

    public function isAdmin(User $user, Workspace $workspace): bool
    {
        return $this->roleFor($user, $workspace) === 'ADMIN';
    }

    public function createInitialWorkspace(User $owner, string $name): Workspace
    {
        return DB::transaction(function () use ($owner, $name): Workspace {
            $workspace = Workspace::query()->create([
                'name' => $name,
                'slug' => $this->uniqueSlug($name),
                'description' => 'Productivity-first issue tracking workspace',
                'created_by' => $owner->getKey(),
            ]);

            $workspace->memberships()->create([
                'user_id' => $owner->getKey(),
                'role' => 'ADMIN',
                'joined_at' => now(),
            ]);

            $this->seedDefaultStatuses($workspace);
            $this->seedDefaultLabels($workspace);

            return $workspace->fresh(['memberships.user', 'labels', 'statuses']);
        });
    }

    public function seedDefaultStatuses(Workspace $workspace): void
    {
        $statuses = [
            ['name' => 'Backlog', 'type' => 'BACKLOG', 'color' => '#71809A', 'position' => 1, 'is_default' => true],
            ['name' => 'Todo', 'type' => 'TODO', 'color' => '#5CA9FF', 'position' => 2, 'is_default' => false],
            ['name' => 'In Progress', 'type' => 'IN_PROGRESS', 'color' => '#F3C969', 'position' => 3, 'is_default' => false],
            ['name' => 'In Review', 'type' => 'IN_REVIEW', 'color' => '#C48DFF', 'position' => 4, 'is_default' => false],
            ['name' => 'Done', 'type' => 'DONE', 'color' => '#38D39F', 'position' => 5, 'is_default' => false],
        ];

        foreach ($statuses as $status) {
            IssueStatus::query()->firstOrCreate(
                [
                    'workspace_id' => $workspace->getKey(),
                    'name' => $status['name'],
                ],
                $status + ['workspace_id' => $workspace->getKey()],
            );
        }
    }

    public function seedDefaultLabels(Workspace $workspace): void
    {
        $labels = [
            ['name' => 'Bug', 'color' => '#FF6B81', 'description' => 'Broken or incorrect behavior'],
            ['name' => 'Feature', 'color' => '#5CA9FF', 'description' => 'New capability or improvement'],
            ['name' => 'Design', 'color' => '#C48DFF', 'description' => 'UX and interface work'],
            ['name' => 'Infra', 'color' => '#38D39F', 'description' => 'Platform and deployment work'],
        ];

        foreach ($labels as $label) {
            Label::query()->firstOrCreate(
                [
                    'workspace_id' => $workspace->getKey(),
                    'name' => $label['name'],
                ],
                $label + ['workspace_id' => $workspace->getKey()],
            );
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'workspace';
        $slug = $base;
        $counter = 2;

        while (Workspace::query()->where('slug', $slug)->exists()) {
            $slug = sprintf('%s-%d', $base, $counter);
            $counter++;
        }

        return $slug;
    }
}

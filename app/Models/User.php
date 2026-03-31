<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'job_title',
        'avatar_path',
        'timezone',
        'bio',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<TeamMember, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * @return BelongsToMany<Workspace, $this>
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'team_members')
            ->withPivot(['id', 'role', 'joined_at', 'created_at', 'updated_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Project, $this>
     */
    public function leadProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'lead_user_id');
    }

    /**
     * @return HasMany<Issue, $this>
     */
    public function assignedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'assignee_id');
    }

    /**
     * @return HasMany<Issue, $this>
     */
    public function reportedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'reporter_id');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    /**
     * @return HasMany<ActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'actor_id');
    }

    public function workspaceRole(Workspace $workspace): ?string
    {
        return $this->memberships
            ->firstWhere('workspace_id', $workspace->getKey())
            ?->role;
    }

    public function currentWorkspace(): ?Workspace
    {
        return $this->workspaces()->orderBy('name')->first();
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function orderedWorkspaces(): Collection
    {
        return $this->workspaces()->orderBy('name')->get();
    }
}

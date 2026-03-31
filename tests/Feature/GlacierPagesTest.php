<?php

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use App\Models\User;

test('guest auth pages render successfully', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Track issues with crystal clarity');

    $this->get('/register')
        ->assertOk()
        ->assertSee('Create your Glacier workspace');

    $this->get('/forgot-password')
        ->assertOk()
        ->assertSee('Reset access without losing momentum');
});

test('demo credentials can log into the issue tracker', function () {
    $this->seed();

    $this->post('/login', [
        'email' => 'zahra@glacier.app',
        'password' => 'preview-demo',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticated();
});

test('authenticated users can browse the seeded tracker pages', function () {
    $this->seed();

    $user = User::query()->where('email', 'zahra@glacier.app')->firstOrFail();
    $project = Project::query()->firstOrFail();
    $issue = Issue::query()->firstOrFail();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Priority Queue');

    $this->actingAs($user)
        ->get('/issues')
        ->assertOk()
        ->assertSee('Issue List')
        ->assertSee($issue->title);

    $this->actingAs($user)
        ->get(route('issues.show', $issue))
        ->assertOk()
        ->assertSee($issue->title)
        ->assertSee('Activity Timeline');

    $this->actingAs($user)
        ->get('/projects')
        ->assertOk()
        ->assertSee('Project List')
        ->assertSee($project->name);

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee($project->name)
        ->assertSee('Milestones');

    $this->actingAs($user)
        ->get('/kanban?project='.$project->getKey())
        ->assertOk()
        ->assertSee('Kanban Board');
});

test('graphql me and issues queries return seeded tracker data', function () {
    $this->seed();

    $user = User::query()->where('email', 'zahra@glacier.app')->firstOrFail();
    $project = Project::query()->firstOrFail();

    $response = $this->actingAs($user)->postJson('/graphql', [
        'query' => <<<'GRAPHQL'
            query TrackerOverview($workspaceId: ID!, $projectId: ID!) {
              me {
                name
                email
              }
              projects(workspaceId: $workspaceId) {
                id
                name
              }
              issues(projectId: $projectId) {
                totalCount
                nodes {
                  id
                  title
                }
              }
            }
        GRAPHQL,
        'variables' => [
            'workspaceId' => $user->currentWorkspace()?->getKey(),
            'projectId' => $project->getKey(),
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.me.email', 'zahra@glacier.app')
        ->assertJson(fn ($json) => $json
            ->whereType('data.projects', 'array')
            ->whereType('data.issues.totalCount', 'integer'));
});

test('graphql moveIssueStatus mutation updates issue status', function () {
    $this->seed();

    $user = User::query()->where('email', 'zahra@glacier.app')->firstOrFail();
    $issue = Issue::query()->firstOrFail();
    $status = IssueStatus::query()->where('name', 'Done')->firstOrFail();

    $this->actingAs($user)->postJson('/graphql', [
        'query' => <<<'GRAPHQL'
            mutation MoveIssue($issueId: ID!, $statusId: ID!) {
              moveIssueStatus(issueId: $issueId, statusId: $statusId) {
                id
                status {
                  name
                  type
                }
              }
            }
        GRAPHQL,
        'variables' => [
            'issueId' => $issue->getKey(),
            'statusId' => $status->getKey(),
        ],
    ])->assertOk()
        ->assertJsonPath('data.moveIssueStatus.status.name', 'Done');

    expect($issue->fresh()->status_id)->toBe($status->getKey());
});

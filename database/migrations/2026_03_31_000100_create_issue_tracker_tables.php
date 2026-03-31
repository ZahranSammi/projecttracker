<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->uuid('user_id');
            $table->string('role', 20);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->string('key', 12);
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('lead_user_id')->nullable();
            $table->string('icon_path')->nullable();
            $table->string('network_path')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'key']);
            $table->index(['workspace_id', 'is_archived']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('lead_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('issue_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->string('name');
            $table->string('type', 24);
            $table->string('color', 16);
            $table->unsignedInteger('position');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['workspace_id', 'name']);
            $table->index(['workspace_id', 'position']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
        });

        Schema::create('labels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->string('name');
            $table->string('color', 16);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'name']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
        });

        Schema::create('issues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->uuid('project_id');
            $table->unsignedBigInteger('number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('status_id');
            $table->string('priority', 20);
            $table->string('type', 32)->default('TASK');
            $table->uuid('assignee_id')->nullable();
            $table->uuid('reporter_id');
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_id', 'number']);
            $table->index(['workspace_id', 'project_id', 'status_id']);
            $table->index(['workspace_id', 'assignee_id']);
            $table->index(['workspace_id', 'reporter_id']);
            $table->index(['workspace_id', 'priority']);
            $table->index(['workspace_id', 'due_date']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('status_id')->references('id')->on('issue_statuses')->restrictOnDelete();
            $table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('reporter_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('issue_labels', function (Blueprint $table) {
            $table->uuid('issue_id');
            $table->uuid('label_id');
            $table->timestamps();

            $table->primary(['issue_id', 'label_id']);
            $table->foreign('issue_id')->references('id')->on('issues')->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on('labels')->cascadeOnDelete();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->uuid('issue_id');
            $table->uuid('author_id');
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['issue_id', 'created_at']);
            $table->index(['author_id', 'created_at']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('issue_id')->references('id')->on('issues')->cascadeOnDelete();
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->uuid('project_id')->nullable();
            $table->uuid('issue_id')->nullable();
            $table->uuid('comment_id')->nullable();
            $table->uuid('actor_id')->nullable();
            $table->string('entity_type');
            $table->uuid('entity_id')->nullable();
            $table->string('action');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['workspace_id', 'created_at']);
            $table->index(['project_id', 'created_at']);
            $table->index(['issue_id', 'created_at']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('issue_id')->references('id')->on('issues')->nullOnDelete();
            $table->foreign('comment_id')->references('id')->on('comments')->nullOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('issue_labels');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('issue_statuses');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('workspaces');
    }
};

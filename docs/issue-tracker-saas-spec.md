# Issue Tracker SaaS System Specification

## 1. Design System Overview

### Product Positioning
- Product name: `Vector`
- Category: multi-tenant issue tracker for software teams
- Audience: startup teams, product engineers, founders, and technical PMs
- Product goal: a fast, low-friction planning and delivery workspace inspired by Linear's speed, GitHub Issues' clarity, and Jira's structured workflows

### Core Experience Principles
- Fast capture: creating and triaging issues should take seconds, not minutes
- Context-rich detail: every issue acts as a durable source of truth with comments, activity, labels, assignee, and due dates
- Workspace-first collaboration: users belong to a workspace, work inside projects, and view activity across the team
- Opinionated clarity: fewer controls on each screen, stronger defaults, consistent keyboard-first interactions
- Production-ready IA: all pages support empty, loading, and failure states without leaving broken UI gaps

### Visual Direction
- Layout: left sidebar + sticky top navbar + content canvas
- Tone: dark, minimal, technical, distraction-free
- Visual references: Linear, GitHub, Notion, and modern SaaS dashboard patterns
- Density: medium-compact, optimized for professional daily use
- Contrast: high-contrast text with muted surfaces and subtle accent colors

### Suggested Design Tokens
- Background: `#0B1020`
- Surface: `#11182B`
- Surface elevated: `#161F36`
- Border: `#24304A`
- Text primary: `#F3F7FF`
- Text secondary: `#A7B3C9`
- Text muted: `#71809A`
- Accent blue: `#5CA9FF`
- Accent green: `#38D39F`
- Accent yellow: `#F3C969`
- Accent red: `#FF6B81`
- Focus ring: `#7CC0FF`

### Typography
- UI font: `Geist` or `Satoshi`
- Mono font: `IBM Plex Mono`
- Heading style: tight tracking, medium to semibold weight
- Data density: use tabular numerals for counters, dates, and issue ids

### Reusable UI Components
- App shell with collapsible workspace sidebar
- Global command palette
- Workspace switcher
- Breadcrumb header
- Metric cards
- Filter chips and saved views
- Data table with bulk actions
- Issue status pill
- Priority badge
- Avatar group
- Activity timeline
- Kanban column and draggable issue card
- Rich text editor for issue descriptions and comments
- Modal, sheet, toast, inline banner, skeleton block, empty state card

### Interaction Model
- Keyboard shortcuts for create issue, search, assign, change status
- Inline editing for title, status, priority, labels, assignee
- Drawer or modal for quick issue create/edit
- Optimistic UI for status moves, assignee updates, and comments
- Infinite scroll or cursor pagination for activity feeds

## 2. Complete Page List

### 1. Login
- Two-column auth layout with product value statement on the left and form on the right
- Fields: email, password
- Actions: sign in, sign in with GitHub, link to register, link to forgot password
- UX details: caps lock hint, inline validation, loading button state

### 2. Register
- Fields: full name, workspace name, email, password, confirm password
- Optional: invite code or team join link
- On success: create workspace and land on onboarding dashboard

### 3. Forgot Password
- Step 1: enter email
- Step 2: confirmation state that reset link was sent
- Optional follow-up page: reset password with token, new password, confirm password

### 4. Dashboard
- Purpose: team health overview
- Sections:
  - top summary cards: total open issues, overdue issues, issues assigned to me, completed this week
  - project progress list with open/closed counts
  - recent activity feed
  - my work widget: assigned, due soon, mentioned
  - status distribution chart
- Quick actions: new issue, new project, invite member

### 5. Project List
- Table or card-grid of projects
- Columns: name, key, lead, open issues, completed issues, members, updated at
- Controls: create project, search, filter by archived/active, sort by updated
- Empty state: create the first project

### 6. Project Detail
- Header: project icon, name, key, description, members, settings access
- Tabs: overview, issues, board, activity, settings
- Overview widgets:
  - issue stats by status
  - velocity summary for last 14 days
  - recent activity
  - label usage

### 7. Issue List
- Dense table view optimized for triage
- Columns: issue key, title, status, priority, assignee, labels, due date, updated at
- Controls:
  - search by title and description
  - filters: status, priority, assignee, reporter, label, due date, created by me
  - sorting: updated, created, priority, due date
  - pagination or cursor-based "load more"
  - saved views
- Bulk actions: assign, change status, add label, delete

### 8. Issue Detail
- Split detail layout:
  - main panel: title, description, comment thread, linked activity timeline
  - right panel: status, priority, assignee, reporter, labels, due date, created/updated timestamps
- Features:
  - markdown or rich text description
  - threaded comments or flat comments with mentions
  - timeline entries for every field change
  - related issues and project breadcrumb

### 9. Kanban Board
- Columns mapped to project statuses such as Backlog, Todo, In Progress, In Review, Done
- Card content: key, title, labels, assignee avatar, priority
- Behaviors:
  - drag and drop between columns
  - optimistic move with rollback on error
  - WIP count per column
  - filter bar synced with issue list

### 10. Create/Edit Issue Modal
- Used from global quick-add or project context
- Fields:
  - title
  - description
  - project
  - status
  - priority
  - assignee
  - reporter
  - labels
  - due date
- Save as draft behavior is optional; default path is immediate create
- Validation: title required, project required

### 11. User Profile
- User card with avatar, display name, email, timezone, role, recent activity
- Tabs:
  - profile
  - assigned issues
  - notifications
- Editable fields: name, avatar, bio, timezone

### 12. Settings
- Scoped settings areas:
  - workspace settings
  - member management
  - project settings
  - status workflow settings
  - label management
  - security and sessions
- Admin-only controls: invite/remove members, update workspace branding, project archive

### 13. Empty States
- Dashboard empty: no projects yet, CTA to create first project
- Issue list empty: no issues match current filters, CTA to clear filters
- Comments empty: prompt to start the discussion
- Kanban empty column: subtle helper text to drag or create issues

### 14. Loading States
- Auth form submit spinner
- Dashboard cards with skeleton shimmer
- Table row skeletons
- Issue detail panel skeleton with placeholder timeline and comment bubbles
- Kanban column skeleton cards

### 15. Error / 404 States
- 404 page with search and back-to-dashboard CTA
- Permission denied page for unauthorized workspace or admin route access
- Inline API failure banners in tables, forms, and drag-and-drop interactions
- Retry affordance for transient network failures

### Global Navigation
- Sidebar:
  - dashboard
  - my issues
  - projects
  - team activity
  - settings
- Top navbar:
  - workspace switcher
  - global search
  - create issue
  - notifications
  - profile menu

## 3. Backend Architecture Summary

### High-Level Architecture
- Application framework: Laravel monolith as the source of truth
- Frontend delivery: Inertia.js + React mounted from Laravel routes
- API: GraphQL endpoint exposed from Laravel, preferably via Lighthouse at `/graphql`
- Database: PostgreSQL
- Auth: Laravel session auth for the web app, optionally Sanctum tokens for API consumers
- Cache: Redis for session, rate limiting, queues, and dashboard aggregates
- Background jobs: Laravel queues for notifications, digest emails, and audit trail fan-out
- File storage: S3-compatible object storage for avatars and attachment metadata

### Suggested Service Boundaries
- `Auth Service`
  - registration, login, password reset, session management
- `Workspace Service`
  - workspace lifecycle, membership, roles
- `Project Service`
  - project CRUD, project members, project metrics
- `Issue Service`
  - issue CRUD, filters, board moves, assignment, label links
- `Comment Service`
  - create and list comments, mention extraction
- `Activity Service`
  - append-only audit events for issue and project changes
- `Notification Service`
  - in-app and email notifications

### Laravel Application Layers
- `HTTP Layer`
  - Laravel web routes for Inertia page entry points
  - GraphQL endpoint for application data access
  - form requests and middleware for validation, auth, workspace context, and rate limiting
- `Domain Layer`
  - service classes for workspaces, projects, issues, comments, and activity writes
  - transaction boundaries for multi-step mutations
- `Persistence Layer`
  - Eloquent models, query scopes, observers, and repositories where query complexity justifies separation
- `Presentation Layer`
  - Inertia pages and React components rendered from Laravel controllers
  - GraphQL resolvers returning structured data for interactive client features

### Request Flow
1. Laravel web routes return Inertia pages for authenticated app screens.
2. React components fetch application data through the Laravel-hosted GraphQL endpoint.
3. GraphQL resolvers validate auth, workspace membership, and policy access.
4. Service classes orchestrate business rules and transactional writes.
5. Eloquent persists canonical records in PostgreSQL.
6. Activity log records are written in the same transaction as important mutations.
7. Queued jobs dispatch notifications after commit.

### Permission Model
- Workspace role enum: `ADMIN`, `MEMBER`
- `ADMIN`
  - manage workspace settings
  - invite/remove members
  - create/update/archive projects
  - manage labels and workflow statuses
- `MEMBER`
  - create and update issues
  - comment on issues
  - move issues across statuses unless a project restricts certain transitions

### Key Behavioral Rules
- Every project belongs to exactly one workspace
- Every issue belongs to exactly one project
- Every issue references a status and priority snapshot value
- Dragging an issue across Kanban columns calls `moveIssueStatus`
- `moveIssueStatus` writes both the issue update and an activity log record atomically
- Search should support title and description, with `tsvector` full-text search in PostgreSQL

### Deployment Shape
- Laravel application deployed as PHP-FPM or Octane behind Nginx
- Inertia frontend assets bundled with Vite and served by Laravel
- GraphQL API served by the same Laravel application
- PostgreSQL managed instance
- Redis managed instance
- Object storage for uploads
- Observability: OpenTelemetry, structured logs, Sentry

## 4. GraphQL Schema / Domain Overview

### Core Relationships
- A `User` belongs to many `Workspaces` through `TeamMember`
- A `Workspace` has many `Projects`, `Users`, `Labels`, and `ActivityLogs`
- A `Project` has many `Issues`
- An `Issue` belongs to one `Project`, one `Workspace`, one `Reporter`, optional one `Assignee`, many `Labels`, many `Comments`, many `ActivityLogs`
- A `Comment` belongs to one `Issue` and one `User`
- An `ActivityLog` belongs to a workspace and can optionally reference a project, issue, comment, and actor

### Main GraphQL Types

```graphql
scalar DateTime
scalar JSON

enum Role {
  ADMIN
  MEMBER
}

enum IssuePriority {
  LOW
  MEDIUM
  HIGH
  URGENT
}

enum IssueStatusType {
  BACKLOG
  TODO
  IN_PROGRESS
  IN_REVIEW
  DONE
  CANCELED
}

type User {
  id: ID!
  name: String!
  email: String!
  avatarUrl: String
  timezone: String
  bio: String
  memberships: [TeamMember!]!
  assignedIssues(
    projectId: ID
    pagination: PaginationInput
  ): IssueConnection!
  createdAt: DateTime!
  updatedAt: DateTime!
}

type Workspace {
  id: ID!
  name: String!
  slug: String!
  role: Role!
  members: [TeamMember!]!
  projects: [Project!]!
  labels: [Label!]!
  createdAt: DateTime!
  updatedAt: DateTime!
}

type TeamMember {
  id: ID!
  role: Role!
  user: User!
  workspace: Workspace!
  joinedAt: DateTime!
}

type Project {
  id: ID!
  workspaceId: ID!
  key: String!
  name: String!
  description: String
  isArchived: Boolean!
  issueStats: ProjectIssueStats!
  issues(filters: IssueFilterInput, pagination: PaginationInput): IssueConnection!
  createdAt: DateTime!
  updatedAt: DateTime!
}

type ProjectIssueStats {
  total: Int!
  backlog: Int!
  todo: Int!
  inProgress: Int!
  inReview: Int!
  done: Int!
  overdue: Int!
}

type Issue {
  id: ID!
  projectId: ID!
  workspaceId: ID!
  number: Int!
  identifier: String!
  title: String!
  description: String
  status: IssueStatus!
  priority: IssuePriority!
  assignee: User
  reporter: User!
  labels: [Label!]!
  dueDate: DateTime
  comments(pagination: PaginationInput): CommentConnection!
  activity(pagination: PaginationInput): ActivityLogConnection!
  createdAt: DateTime!
  updatedAt: DateTime!
}

type IssueStatus {
  id: ID!
  name: String!
  type: IssueStatusType!
  color: String!
  position: Int!
}

type Comment {
  id: ID!
  issueId: ID!
  author: User!
  body: String!
  createdAt: DateTime!
  updatedAt: DateTime!
}

type Label {
  id: ID!
  workspaceId: ID!
  name: String!
  color: String!
  description: String
}

type ActivityLog {
  id: ID!
  workspaceId: ID!
  actor: User
  entityType: String!
  entityId: ID!
  action: String!
  metadata: JSON
  createdAt: DateTime!
}

type AuthPayload {
  token: String!
  refreshToken: String
  user: User!
  workspace: Workspace!
}

type PageInfo {
  hasNextPage: Boolean!
  endCursor: String
}

type IssueEdge {
  cursor: String!
  node: Issue!
}

type IssueConnection {
  edges: [IssueEdge!]!
  pageInfo: PageInfo!
  totalCount: Int!
}

type CommentEdge {
  cursor: String!
  node: Comment!
}

type CommentConnection {
  edges: [CommentEdge!]!
  pageInfo: PageInfo!
  totalCount: Int!
}

type ActivityLogEdge {
  cursor: String!
  node: ActivityLog!
}

type ActivityLogConnection {
  edges: [ActivityLogEdge!]!
  pageInfo: PageInfo!
  totalCount: Int!
}
```

### Input Types

```graphql
input PaginationInput {
  first: Int = 20
  after: String
}

input IssueFilterInput {
  statusIds: [ID!]
  priorities: [IssuePriority!]
  assigneeIds: [ID!]
  reporterIds: [ID!]
  labelIds: [ID!]
  dueDateFrom: DateTime
  dueDateTo: DateTime
  includeCompleted: Boolean
}

input RegisterInput {
  name: String!
  workspaceName: String!
  email: String!
  password: String!
}

input CreateProjectInput {
  workspaceId: ID!
  name: String!
  key: String!
  description: String
}

input UpdateProjectInput {
  name: String
  description: String
  isArchived: Boolean
}

input CreateIssueInput {
  projectId: ID!
  title: String!
  description: String
  statusId: ID
  priority: IssuePriority = MEDIUM
  assigneeId: ID
  reporterId: ID
  labelIds: [ID!]
  dueDate: DateTime
}

input UpdateIssueInput {
  title: String
  description: String
  statusId: ID
  priority: IssuePriority
  assigneeId: ID
  labelIds: [ID!]
  dueDate: DateTime
}

input AddCommentInput {
  issueId: ID!
  body: String!
}

input UpdateProfileInput {
  name: String
  avatarUrl: String
  timezone: String
  bio: String
}
```

### Required Queries

```graphql
type Query {
  me: User
  projects(workspaceId: ID!, search: String, pagination: PaginationInput): [Project!]!
  project(id: ID!): Project
  issues(
    projectId: ID!
    filters: IssueFilterInput
    search: String
    sortBy: String = "updatedAt"
    sortDirection: String = "DESC"
    pagination: PaginationInput
  ): IssueConnection!
  issue(id: ID!): Issue
  myAssignedIssues(
    workspaceId: ID!
    filters: IssueFilterInput
    pagination: PaginationInput
  ): IssueConnection!
  activityFeed(
    workspaceId: ID!
    projectId: ID
    pagination: PaginationInput
  ): ActivityLogConnection!
}
```

### Required Mutations

```graphql
type Mutation {
  login(email: String!, password: String!): AuthPayload!
  register(input: RegisterInput!): AuthPayload!
  createProject(input: CreateProjectInput!): Project!
  updateProject(id: ID!, input: UpdateProjectInput!): Project!
  createIssue(input: CreateIssueInput!): Issue!
  updateIssue(id: ID!, input: UpdateIssueInput!): Issue!
  deleteIssue(id: ID!): Boolean!
  assignIssue(issueId: ID!, assigneeId: ID): Issue!
  moveIssueStatus(issueId: ID!, statusId: ID!): Issue!
  addComment(input: AddCommentInput!): Comment!
  updateProfile(input: UpdateProfileInput!): User!
}
```

### Resolver Notes
- `me` resolves the authenticated user plus workspace memberships
- `projects` is scoped to a workspace and should exclude archived projects by default unless a filter says otherwise
- `issues` must combine project scoping, full-text search, filters, sorting, and cursor pagination
- `myAssignedIssues` is a filtered `issues` query for the current user
- `moveIssueStatus` must verify the target status belongs to the same project workflow or workspace workflow
- `deleteIssue` should soft delete or archive if audit requirements are strict; if hard deleting, preserve activity in an audit warehouse

### Suggested Optional Extensions
- `Subscription.issueUpdated(projectId: ID!): Issue!`
- `Mutation.createLabel`
- `Mutation.inviteMember`
- `Mutation.archiveProject`

## 5. PostgreSQL Table Structure

### Modeling Notes
- Use `uuid` primary keys for global entities
- Use `bigint` sequence for human-friendly issue numbers per project
- Use `timestamptz` for all temporal fields
- Use `jsonb` for audit metadata
- Add soft delete support where business recovery matters

### `users`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| name | varchar(160) | not null |
| email | varchar(255) | not null, unique |
| password_hash | text | not null |
| avatar_url | text | null |
| timezone | varchar(64) | null |
| bio | text | null |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |

Indexes:
- unique on `email`

### `workspaces`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| name | varchar(160) | not null |
| slug | varchar(120) | not null, unique |
| created_by | uuid | FK -> users.id |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |

Indexes:
- unique on `slug`
- index on `created_by`

### `team_members`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| user_id | uuid | not null, FK -> users.id |
| role | varchar(20) | not null check role in ('ADMIN','MEMBER') |
| joined_at | timestamptz | not null default now() |

Indexes:
- unique on `(workspace_id, user_id)`
- index on `(user_id, workspace_id)`

### `projects`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| key | varchar(12) | not null |
| name | varchar(160) | not null |
| description | text | null |
| is_archived | boolean | not null default false |
| created_by | uuid | FK -> users.id |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |

Indexes:
- unique on `(workspace_id, key)`
- index on `(workspace_id, is_archived)`

### `issue_statuses`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| name | varchar(80) | not null |
| type | varchar(24) | not null |
| color | varchar(16) | not null |
| position | integer | not null |
| is_default | boolean | not null default false |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |

Indexes:
- unique on `(workspace_id, name)`
- index on `(workspace_id, position)`

### `labels`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| name | varchar(80) | not null |
| color | varchar(16) | not null |
| description | text | null |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |

Indexes:
- unique on `(workspace_id, name)`

### `issues`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| project_id | uuid | not null, FK -> projects.id |
| number | bigint | not null |
| title | varchar(255) | not null |
| description | text | null |
| status_id | uuid | not null, FK -> issue_statuses.id |
| priority | varchar(20) | not null check priority in ('LOW','MEDIUM','HIGH','URGENT') |
| assignee_id | uuid | null, FK -> users.id |
| reporter_id | uuid | not null, FK -> users.id |
| due_date | timestamptz | null |
| search_vector | tsvector | generated or maintained by trigger |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |
| deleted_at | timestamptz | null |

Indexes:
- unique on `(project_id, number)`
- index on `(workspace_id, project_id, status_id)`
- index on `(workspace_id, assignee_id)`
- index on `(workspace_id, reporter_id)`
- index on `(workspace_id, priority)`
- index on `(workspace_id, due_date)`
- GIN index on `search_vector`

### `issue_labels`
| Column | Type | Constraints |
|---|---|---|
| issue_id | uuid | PK part, FK -> issues.id |
| label_id | uuid | PK part, FK -> labels.id |
| created_at | timestamptz | not null default now() |

Indexes:
- primary key on `(issue_id, label_id)`
- index on `label_id`

### `comments`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| issue_id | uuid | not null, FK -> issues.id |
| author_id | uuid | not null, FK -> users.id |
| body | text | not null |
| created_at | timestamptz | not null default now() |
| updated_at | timestamptz | not null default now() |
| deleted_at | timestamptz | null |

Indexes:
- index on `(issue_id, created_at)`
- index on `(author_id, created_at)`

### `activity_logs`
| Column | Type | Constraints |
|---|---|---|
| id | uuid | PK |
| workspace_id | uuid | not null, FK -> workspaces.id |
| project_id | uuid | null, FK -> projects.id |
| issue_id | uuid | null, FK -> issues.id |
| comment_id | uuid | null, FK -> comments.id |
| actor_id | uuid | null, FK -> users.id |
| entity_type | varchar(40) | not null |
| entity_id | uuid | not null |
| action | varchar(80) | not null |
| metadata | jsonb | not null default '{}'::jsonb |
| created_at | timestamptz | not null default now() |

Indexes:
- index on `(workspace_id, created_at desc)`
- index on `(project_id, created_at desc)`
- index on `(issue_id, created_at desc)`
- GIN index on `metadata`

### Recommended Supporting Tables
- `refresh_tokens`
- `password_resets`
- `notifications`
- `project_members` if projects need explicit access control beyond workspace membership
- `attachments` if file uploads are required

### Example SQL DDL Snippet

```sql
create table issues (
  id uuid primary key,
  workspace_id uuid not null references workspaces(id),
  project_id uuid not null references projects(id),
  number bigint not null,
  title varchar(255) not null,
  description text,
  status_id uuid not null references issue_statuses(id),
  priority varchar(20) not null check (priority in ('LOW','MEDIUM','HIGH','URGENT')),
  assignee_id uuid references users(id),
  reporter_id uuid not null references users(id),
  due_date timestamptz,
  search_vector tsvector,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz,
  unique (project_id, number)
);

create index idx_issues_workspace_project_status
  on issues (workspace_id, project_id, status_id);

create index idx_issues_search_vector
  on issues using gin (search_vector);
```

## 6. UX States

### Empty States
- Dashboard:
  - headline: `Start tracking work`
  - body: create a project and invite teammates to begin triaging issues
  - CTA: `Create Project`
- Project issues:
  - headline: `No issues yet`
  - body: add your first task, bug, or feature request
  - CTA: `New Issue`
- Filtered list:
  - headline: `No matching issues`
  - body: try clearing filters or adjusting search terms
  - CTA: `Clear Filters`

### Loading States
- Use skeletons that mirror final layout instead of centered spinners
- Preserve header actions while content loads
- For board view, keep column scaffolding visible so users retain context
- For optimistic issue updates, show inline pending indicators on the affected card or row

### Error States
- List fetch failure: inline banner with `Retry`
- Form mutation failure: field-level error plus toast summary
- Permission failure: dedicated guard screen with `Contact your admin`
- Not found: issue or project 404 state with quick search and dashboard link

### Accessibility Notes
- Full keyboard navigation for sidebar, tables, filters, and Kanban cards
- Focus-visible ring on all interactive components
- Minimum contrast ratio 4.5:1 for text
- Drag-and-drop must have keyboard fallback via "Move to status" action menu

## 7. Notes for Laravel Implementation

### Recommended Stack
- Backend framework: Laravel
- Frontend delivery: Inertia.js + React
- API transport: GraphQL served from Laravel
- Database: PostgreSQL
- Queues and cache: Redis
- Asset bundling: Vite

### Laravel Application Structure
- `routes/web.php`
  - auth screens, dashboard screens, project screens, issue detail screens, settings screens
- `routes/console.php`
  - scheduled jobs for digests and cleanup
- `app/Models`
  - `User`, `Workspace`, `TeamMember`, `Project`, `Issue`, `IssueStatus`, `Comment`, `Label`, `ActivityLog`
- `app/Policies`
  - `WorkspacePolicy`, `ProjectPolicy`, `IssuePolicy`, `CommentPolicy`
- `app/Services`
  - `AuthService`, `WorkspaceService`, `ProjectService`, `IssueService`, `CommentService`, `ActivityLogService`
- `app/GraphQL`
  - queries, mutations, shared resolvers, input validators, and type-specific field resolvers
- `app/Http/Controllers`
  - thin Inertia controllers for page entry points and auth flows
- `resources/js/Pages`
  - `Auth/Login.jsx`
  - `Auth/Register.jsx`
  - `Auth/ForgotPassword.jsx`
  - `Dashboard/Index.jsx`
  - `Projects/Index.jsx`
  - `Projects/Show.jsx`
  - `Issues/Show.jsx`
  - `Settings/Index.jsx`
- `resources/js/Components`
  - layout, board, issue table, issue modal, activity feed, filters, charts, and shared UI primitives
- `database/migrations`
  - workspace, membership, project, issue, comment, label, and activity tables
- `database/seeders`
  - demo workspace, default statuses, sample labels, and test issues

### Migrations
- Create migrations for:
  - `users`
  - `workspaces`
  - `team_members`
  - `projects`
  - `issue_statuses`
  - `labels`
  - `issues`
  - `issue_labels`
  - `comments`
  - `activity_logs`
  - optional `notifications`, `attachments`, `personal_access_tokens`
- Use PostgreSQL-native types where appropriate:
  - `uuid`
  - `jsonb`
  - `timestamptz`
  - generated or trigger-maintained `tsvector` for issue search
- Add unique and composite indexes in migrations to support:
  - workspace scoping
  - issue list filters
  - board status queries
  - full-text search

### Eloquent Models
- `User`
  - has many team memberships, assigned issues, reported issues, comments, and activity logs
- `Workspace`
  - has many members, projects, statuses, labels, and activity logs
- `TeamMember`
  - belongs to user and workspace, stores role
- `Project`
  - belongs to workspace, has many issues
- `Issue`
  - belongs to workspace, project, status, assignee, reporter
  - belongs to many labels through `issue_labels`
  - has many comments and activity logs
- `Comment`
  - belongs to issue, workspace, and author
- `Label`
  - belongs to workspace, belongs to many issues
- `ActivityLog`
  - belongs to workspace and optionally actor, project, issue, or comment

### Eloquent Query Patterns
- Add reusable scopes:
  - `forWorkspace($workspaceId)`
  - `active()`
  - `assignedTo($userId)`
  - `search($term)`
  - `withIssueListRelations()`
- Use eager loading for:
  - assignee
  - reporter
  - labels
  - status
  - recent comments where needed
- Consider model observers or explicit service hooks for audit log creation

### Policies and Authorization
- Use Laravel policies as the primary authorization layer
- `WorkspacePolicy`
  - manage workspace settings, invites, labels, and workflow
- `ProjectPolicy`
  - create, update, archive, and view projects
- `IssuePolicy`
  - create, update, assign, move status, delete, and view issues
- `CommentPolicy`
  - create, update, and delete comments
- Map workspace role checks into policies rather than scattering role conditionals across resolvers and controllers
- Apply authorization in both:
  - Inertia controllers for page access
  - GraphQL resolvers for field and mutation access

### Service Layer
- Keep controllers and GraphQL resolvers thin
- Centralize business logic in services such as:
  - `IssueService::createIssue`
  - `IssueService::updateIssue`
  - `IssueService::moveStatus`
  - `IssueService::assign`
  - `CommentService::addComment`
  - `ProjectService::createProject`
  - `ActivityLogService::record`
- Service responsibilities:
  - validate cross-entity constraints
  - open database transactions
  - write activity logs
  - dispatch queued notifications
  - normalize label syncing and assignee changes

### GraphQL Resolvers
- Prefer a Laravel-native GraphQL package such as Lighthouse
- Organize schema and resolvers by domain:
  - `app/GraphQL/Queries/MeQuery.php`
  - `app/GraphQL/Queries/ProjectsQuery.php`
  - `app/GraphQL/Queries/IssuesQuery.php`
  - `app/GraphQL/Mutations/LoginMutation.php`
  - `app/GraphQL/Mutations/CreateIssueMutation.php`
  - `app/GraphQL/Mutations/MoveIssueStatusMutation.php`
- Resolver responsibilities:
  - authenticate current user
  - resolve active workspace context
  - authorize via policies
  - call service layer methods
  - transform Eloquent models into schema-friendly shapes where custom fields are needed
- Use dataloaders or eager loading strategies to avoid N+1 problems on:
  - issue assignee
  - reporter
  - labels
  - comments
  - activity actor

### Frontend Integration with Inertia + React
- Use Laravel routes and controllers to render Inertia pages for all major screens
- Use React inside `resources/js` for the application shell and interactive components
- Recommended page structure:
  - `resources/js/Pages/Auth/*`
  - `resources/js/Pages/Dashboard/*`
  - `resources/js/Pages/Projects/*`
  - `resources/js/Pages/Issues/*`
  - `resources/js/Pages/Settings/*`
- Recommended component structure:
  - `resources/js/Components/Layout/*`
  - `resources/js/Components/Issues/*`
  - `resources/js/Components/Board/*`
  - `resources/js/Components/Activity/*`
  - `resources/js/Components/UI/*`
- Data flow:
  - initial page shell and route context from Inertia props
  - GraphQL queries for issue tables, board data, issue detail timelines, and live filters
  - local React state for modal visibility, filter chips, drag-and-drop state, and optimistic updates

### Recommended Frontend Libraries
- GraphQL client: Apollo Client or urql in the Inertia React app
- Drag and drop: `@dnd-kit`
- Forms: `react-hook-form` + `zod`
- Rich text or markdown: TipTap or markdown editor with preview
- Charts: Recharts

### UI Behavior Notes
- Persist current workspace and recent project selection in session, cache, or local storage as appropriate
- Save issue list filters in URL query params for shareable views
- Use debounced search in issue list
- Use optimistic status changes and assignee updates with rollback handling
- Keep create/edit issue as a reusable modal or slide-over mounted in the Inertia app shell

### Suggested Initial MVP Scope
1. Laravel auth, workspace creation, dashboard, project CRUD
2. Issue CRUD, issue list, issue detail, comments
3. Kanban board drag-and-drop
4. Labels, activity feed, profile, settings
5. Notifications, file attachments, subscriptions as phase 2

## Implementation Notes
- This specification assumes Laravel is the application source of truth, with Inertia + React for the dashboard UI, GraphQL for client-server data access, and PostgreSQL for persistence.
- Do not split the product into a separate Node or Next.js application; the web app, GraphQL API, policies, services, jobs, and persistence should live in the Laravel codebase.

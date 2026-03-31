# Glacier Issue Tracker

Glacier Issue Tracker adalah aplikasi issue tracking modern berbasis Laravel untuk tim developer dan startup. Project ini menggabungkan:

- Laravel 13 sebagai application core
- Blade + Tailwind CSS untuk web UI saat ini
- GraphQL via Lighthouse untuk akses data client-server
- PostgreSQL-compatible relational model
- SQLite sebagai default local dev database di repo ini

UI dan domain model-nya dirancang untuk kebutuhan issue tracking ala Jira, Linear, dan GitHub Issues, dengan fokus pada dashboard, project tracking, issue detail, comments, activity timeline, dan kanban board.

## Fitur Yang Sudah Diimplementasikan

- Login, register, forgot password flow di Laravel
- Dashboard workspace dengan metrics, priority queue, team signal, dan activity feed
- Project list dan project detail
- Issue list dan issue detail
- Comment posting pada issue detail
- Kanban board per project
- Workspace, project, issue, label, status, comment, dan activity log data model
- GraphQL endpoint di `/graphql`
- Seeded demo workspace lengkap untuk local preview
- Feature tests untuk browser flow dan GraphQL flow

## Stack

- PHP `^8.3`
- Laravel `^13.0`
- Lighthouse GraphQL `^6.66`
- Tailwind CSS `^4`
- Vite `^8`
- Pest untuk testing

## Struktur Utama

```text
app/
  GraphQL/
  Http/Controllers/
  Models/
  Policies/
  Services/
database/
  migrations/
  seeders/
graphql/
  schema.graphql
resources/
  css/
  js/
  views/
tests/
```

## Menjalankan Project

### 1. Install dependency

```bash
composer install
npm install
```

### 2. Siapkan environment

```bash
copy .env.example .env
php artisan key:generate
```

Repo ini default ke SQLite. Pastikan file database sudah ada:

```bash
type nul > database/database.sqlite
```

Lalu jalankan migration dan seed:

```bash
php artisan migrate:fresh --seed
```

### 3. Jalankan app

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Atau gunakan script dev bawaan:

```bash
composer run dev
```

## Akun Demo

Setelah `php artisan migrate:fresh --seed`, login dengan:

- Email: `zahra@glacier.app`
- Password: `preview-demo`

## Route Utama

- `/login`
- `/register`
- `/forgot-password`
- `/dashboard`
- `/projects`
- `/projects/{project}`
- `/issues`
- `/issues/{issue}`
- `/kanban?project={projectId}`
- `/graphql`

## GraphQL

GraphQL dijalankan dari Laravel melalui Lighthouse.

Endpoint:

```text
/graphql
```

Contoh query:

```graphql
query TrackerOverview($workspaceId: ID!, $projectId: ID!) {
  me {
    name
    email
  }
  projects(workspaceId: $workspaceId) {
    id
    name
    key
  }
  issues(projectId: $projectId) {
    totalCount
    nodes {
      id
      identifier
      title
      priority
      status {
        name
      }
    }
  }
}
```

Mutation utama yang sudah tersedia:

- `login`
- `register`
- `createProject`
- `updateProject`
- `createIssue`
- `updateIssue`
- `deleteIssue`
- `assignIssue`
- `moveIssueStatus`
- `addComment`
- `updateProfile`

Schema GraphQL ada di:

- `graphql/schema.graphql`

## Database Model

Entity inti yang sudah dimodelkan:

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

Model ini disusun agar tetap compatible untuk PostgreSQL production, walaupun local repo saat ini default ke SQLite.

## Testing

Jalankan seluruh test:

```bash
php artisan test
```

Test saat ini mencakup:

- auth page rendering
- login flow
- seeded tracker page rendering
- GraphQL query
- GraphQL mutation

## Catatan Implementasi

- Arsitektur saat ini Laravel-first, bukan app terpisah berbasis Node/Next.js.
- Frontend yang berjalan sekarang masih Blade-based, dengan UI Glacier yang sudah terhubung ke data nyata.
- Spec produk lengkap ada di `docs/issue-tracker-saas-spec.md`.
- Inertia + React masih bisa menjadi langkah berikutnya, tetapi bukan syarat untuk menjalankan app saat ini.

## Next Step Yang Masuk Akal

- tambah settings dan profile pages berbasis data nyata
- tambah create/edit issue modal dari UI
- tambah filter, sorting, dan pagination issue list yang lebih lengkap
- tambah drag and drop kanban di frontend
- pindahkan shell frontend ke Inertia + React jika ingin mengikuti arah frontend yang direkomendasikan di spec

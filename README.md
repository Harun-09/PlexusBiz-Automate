# PlexusBiz Automate

PlexusBiz Automate is a modular Laravel 10 platform for B2B commerce and business automation.  
It combines e-commerce, CRM, marketing automation, social scheduling, workflow rules, support operations, and authenticated REST APIs in one codebase.

## Tech Stack

- Backend: Laravel 10, Sanctum, Spatie Laravel Permission
- Frontend: Inertia.js + React 18 + Tailwind CSS
- Database: MySQL
- PDF: barryvdh/laravel-dompdf
- Build: Vite (`--configLoader native`)

## Solution Scope

This project covers the assignment modules as live, data-backed flows:

- E-Commerce: marketplace, supplier onboarding, product CRUD, stock tracking, MOQ, bulk pricing, cart, checkout, invoices, RFQ
- CRM: customer profiling, purchase history, segmentation, lead management, interaction timeline
- Marketing Automation: template-based campaigns, trigger-based automation, scheduled dispatch
- Social Automation: social accounts, scheduled posts, calendar, publish workflow, engagement placeholders
- Workflow Engine: event-driven IF/THEN automation with logs and scheduler integration
- Admin and RBAC: role-based access, user management, module controls, audit visibility
- Support Automation: tickets, replies, notifications, FAQ matching, chatbot-ready API endpoint

## Architecture

Business logic is organized by domain under `app/Domains`:

- `ECommerce`
- `CRM`
- `Marketing`
- `Social`
- `Workflow`
- `Support`
- `Admin`
- `Settings`
- `Notifications`

Routes are modularized through `App\Providers\DomainServiceProvider` and domain registry configuration in `config/domains.php`.

## Quick Start

### 1) Install dependencies

```bash
composer install
npm install
```

### 2) Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Set MySQL credentials in `.env` (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

### 3) Prepare database and build assets

```bash
php artisan migrate --seed
npm run build
```

### 4) Run locally

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

For hot reload in development:

```bash
npm run dev
php artisan serve --host=127.0.0.1 --port=8000
```

## Demo Accounts

Default seeded accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@plexus.test` | `password` |
| Supplier | `supplier@plexus.test` | `password` |
| Buyer | `buyer@plexus.test` | `password` |
| Marketing Manager | `marketing@plexus.test` | `password` |

Assignment aliases (same password):

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Supplier | `supplier@example.com` | `password` |
| Buyer | `buyer@example.com` | `password` |
| Marketing Manager | `marketing@example.com` | `password` |

## Key Demo Routes

- `GET /products`
- `GET /products/bulk-orders`
- `GET /products/moq-pricing`
- `GET /register-supplier`
- `GET /supplier/products`
- `GET /admin/leads`
- `GET /admin/campaigns`
- `GET /social/posts`
- `GET /social/posts/scheduled`
- `GET /social/calendar`
- `GET /workflow/rules`
- `GET /workflow/logs`
- `GET /buyer/tickets`
- `GET /admin/modules`
- `GET /settings/modules`
- `GET /invoices`

## API Surface

All v1 API routes are authenticated with Sanctum and protected by policies/permissions.

Primary endpoints:

```text
GET  /api/v1/products
GET  /api/v1/orders
GET  /api/v1/customers
GET  /api/v1/campaigns
GET  /api/v1/social-posts
GET  /api/v1/workflow-logs
GET  /api/v1/support-tickets
POST /api/v1/support/chatbot/message
```

Compatibility route also exists:

- `POST /api/support/chatbot/message`

Supported index query params:

- `search`
- `status`
- `per_page` (1-100)
- `sort` (supports descending syntax like `-created_at`)

## Automation and Background Jobs

Workflow execution snapshots are written to `workflow_logs` with:

- `trigger_event`
- `payload` (full JSON snapshot)
- `status`
- `result`
- `error`
- `executed_at`

Runtime commands:

```bash
php artisan queue:work
php artisan schedule:work
php artisan schedule:run
php artisan campaigns:send-scheduled
php artisan carts:check-abandoned
php artisan social-posts:publish-due
php artisan workflow:close-stale-runs
```

These are standard background entrypoints: queued jobs run when code dispatches them, and scheduled tasks run through the scheduler/cron process rather than from web requests.

Daemon examples:

- `deploy/supervisor/laravel-queue.conf`
- `deploy/systemd/laravel-queue.service`
- `deploy/supervisor/laravel-scheduler.conf`
- `deploy/systemd/laravel-scheduler.service`

## Testing and Quality

Run the full test suite:

```bash
php artisan test
```

Build validation:

```bash
npm run build
```

The project includes feature coverage for:

- RBAC and auth access control
- Checkout, inventory, payments, and invoice preview/download
- CRM leads, customers, purchases, interactions
- Marketing campaign triggers and scheduled dispatch
- Social publishing and scheduled jobs
- Workflow rule execution and payload logging
- Support automation and chatbot endpoint
- API resources and workspace module routes

## Operations and Deployment

Deployment and runbook details are documented in:

- [docs/operations.md](docs/operations.md)
- [docs/operations.html](docs/operations.html)

These include:

- queue/scheduler setup
- production deployment checklist
- backup and restore commands
- credential handling guidance

## Assignment Proof Package

Use these documents for evaluator/demo flow:

- [docs/requirement-proof-guide.html](docs/requirement-proof-guide.html)
- [docs/workflow.html](docs/workflow.html)

Database backup artifact:

- `database/backups/plexusbiz_assignment_backup.sql`

## Security Notes

- Keep real provider credentials only in `.env`
- Do not commit secrets
- Mock adapters are used for local/demo-safe email, SMS, Facebook, and Instagram behavior

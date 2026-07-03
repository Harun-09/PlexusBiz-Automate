# PlexusBiz Automate

PlexusBiz Automate is a Laravel 10, React, and Tailwind CSS modular monolith for B2B e-commerce, CRM, marketing automation, social scheduling, workflow automation, support automation, and REST APIs.

## Architecture

The application keeps business logic grouped by domain under `app/Domains`:

- `ECommerce`: suppliers, catalog, pricing tiers, inventory, cart, checkout, orders, invoices, RFQs.
- `CRM`: customer profiles, lifecycle tracking, leads, interactions, segmentation.
- `Marketing`: campaigns, templates, recipients, mockable email providers, dispatch jobs.
- `Social`: social accounts, scheduled posts, mock Facebook/Instagram publishing, engagement placeholders.
- `Workflow`: automation rules, event listeners, queued/sync action execution, full payload snapshots in `workflow_logs`.
- `Support`: support tickets, messages, FAQ matching, auto replies, supplier notifications, chatbot API.
- `Admin`, `Settings`, `Notifications`: route and module boundaries for platform expansion.

Domain routes are registered through `App\Providers\DomainServiceProvider` and the module registry in `config/domains.php`.

## Assignment Coverage

The assignment modules are implemented as live, data-backed flows:

- E-Commerce: multi-vendor marketplace, supplier onboarding, product CRUD, inventory tracking, bulk pricing and MOQ, cart to checkout to confirmation, and invoices.
- CRM: customer registration and profiling, purchase history, basic segmentation, lead management, and interaction history.
- Social Media Automation: scheduled Facebook and Instagram posts, content calendar, rule-based publishing, campaign management, and engagement placeholders.
- Marketing Automation: template-based email campaigns, trigger-based rules, and scheduling. SMS is adapter-ready but not required for the assignment demo.
- Workflow Automation: IF/THEN automation rules, queued and sync execution, scheduler support, and execution logs.
- Admin Panel: RBAC, platform monitoring, user management, and module enable/disable control.
- Order & Support Automation: auto confirmations, support tickets, auto replies, supplier notifications, and chatbot-ready API structure.

Seeded data and feature tests cover the core flows so the platform remains dynamic rather than placeholder-only.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

For local development with hot reload:

```bash
npm run dev
php artisan serve
```

Seeded accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@plexus.test` | `password` |
| Supplier | `supplier@plexus.test` | `password` |
| Buyer | `buyer@plexus.test` | `password` |
| Marketing Manager | `marketing@plexus.test` | `password` |

Assignment aliases are seeded with the same password:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Supplier | `supplier@example.com` | `password` |
| Buyer | `buyer@example.com` | `password` |
| Marketing Manager | `marketing@example.com` | `password` |

Assignment-friendly route aliases are also available for the demo, including `/register-supplier`, `/supplier/products`, `/admin/leads`, `/admin/social-posts`, `/admin/campaigns`, `/admin/automation-rules`, `/admin/modules`, `/buyer/tickets`, and `/customer/profile`.

## Testing

```bash
php artisan test
npm run build
npm audit --audit-level=low
```

Current coverage includes RBAC, checkout and inventory, CRM purchase history, marketing dispatch, social publishing, workflow payload snapshots, support automation, workspace routes, and authenticated API resources.

## API

All v1 API routes use Sanctum authentication and Laravel policies.

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

Index endpoints support:

- `search`
- `status`
- `per_page` from 1 to 100
- `sort`, with `-created_at` style descending fields

The support chatbot endpoint also remains available at `POST /api/support/chatbot/message` for compatibility.

## Automation Logging

Automation execution must preserve trigger context. `WorkflowEngineService` writes every matched, skipped, queued, successful, or failed execution to `workflow_logs` with:

- `trigger_event`
- full JSON `payload`
- `status`
- JSON `result`
- `error`
- `executed_at`

This applies to order placement and support ticket creation events.

## Scheduler Commands

```bash
php artisan schedule:work
php artisan schedule:run
php artisan queue:work
php artisan campaigns:send-scheduled
php artisan carts:check-abandoned
php artisan social-posts:publish-due
```

Daemon examples are in `deploy/supervisor/laravel-scheduler.conf` and `deploy/systemd/laravel-scheduler.service`.

## Requirement Proof Guide

Open `docs/requirement-proof-guide.html` for the full step-by-step viva/demo sequence.

## Operations

See [docs/operations.html](docs/operations.html) for queue workers, scheduler setup, deployment checklist, and backup/restore commands.

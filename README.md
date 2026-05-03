# PlexusBiz Automate

PlexusBiz Automate is a Laravel 10, React, and Tailwind CSS modular monolith for B2B e-commerce, CRM, marketing automation, social scheduling, workflow automation, support automation, and REST APIs.

## Architecture

The application keeps business logic grouped by domain under `app/Domains`:

- `ECommerce`: suppliers, catalog, pricing tiers, inventory, cart, checkout, orders, invoices, RFQs.
- `CRM`: customer profiles, lifecycle tracking, leads, interactions, segmentation.
- `Marketing`: campaigns, templates, recipients, mockable email/SMS providers, dispatch jobs.
- `Social`: social accounts, scheduled posts, mock Facebook/Instagram publishing, engagement placeholders.
- `Workflow`: automation rules, event listeners, queued/sync action execution, full payload snapshots in `workflow_logs`.
- `Support`: support tickets, messages, FAQ matching, auto replies, supplier notifications, chatbot API.
- `Admin`, `Settings`, `Notifications`: route and module boundaries for platform expansion.

Domain routes are registered through `App\Providers\DomainServiceProvider` and the module registry in `config/domains.php`.

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

## Operations

See [docs/operations.md](docs/operations.md) for queue workers, scheduler setup, deployment checklist, and backup/restore commands.

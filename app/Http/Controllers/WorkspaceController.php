<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Enums\UserStatus;
use App\Models\User;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function admin(Request $request): Response
    {
        return $this->page('Admin Control', 'Platform KPIs and operational queues.', [
            ['label' => 'Users', 'value' => User::count()],
            ['label' => 'Suppliers', 'value' => Supplier::count()],
            ['label' => 'Products', 'value' => Product::count()],
            ['label' => 'Revenue', 'value' => number_format((float) Order::sum('grand_total'), 2)],
            ['label' => 'Campaigns', 'value' => Campaign::count()],
            ['label' => 'Scheduled Posts', 'value' => SocialPost::where('status', 'scheduled')->count()],
            ['label' => 'Failed Automations', 'value' => WorkflowLog::where('status', 'failed')->count()],
        ], ['Metric', 'Value'], [], 'Admin KPIs are shown above.');
    }

    public function users(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(UserStatus::class));

        $rows = $this->applyListFilters(User::query()->with('roles'), $filters, ['name', 'email'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (User $user): array => [
            'Name' => $user->name,
            'Email' => $user->email,
            'Roles' => $user->roles->pluck('name')->join(', ') ?: 'unassigned',
            'Status' => $user->status->value,
        ]);

        return $this->page('Users', 'Role assignments and account status.', [], ['Name', 'Email', 'Roles', 'Status'], $rows, filters: $filters);
    }

    public function supplierProducts(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(ProductStatus::class));
        $query = Product::query()->with('supplier.user');

        if ($request->user()->hasRole('supplier')) {
            $query->whereHas('supplier', fn ($supplier) => $supplier->where('user_id', $request->user()->id));
        }

        $rows = $this->applyListFilters($query, $filters, ['sku', 'name'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Product $product): array => [
            'SKU' => $product->sku,
            'Product' => $product->name,
            'Supplier' => $product->supplier?->company_name,
            'Stock' => $product->availableStock(),
            'MOQ' => $product->moq,
            'Status' => $product->status->value,
        ]);

        return $this->page('Product Operations', 'Supplier-owned catalog, stock, MOQ, and status.', [], ['SKU', 'Product', 'Supplier', 'Stock', 'MOQ', 'Status'], $rows, filters: $filters);
    }

    public function commerceOrders(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(OrderStatus::class));
        $query = Order::query()->with(['buyer', 'items.supplier']);

        if ($request->user()->hasRole('buyer')) {
            $query->where('buyer_id', $request->user()->id);
        }

        if ($request->user()->hasRole('supplier')) {
            $supplierId = $request->user()->supplier?->id;
            $query->whereHas('items', fn ($items) => $items->where('supplier_id', $supplierId));
        }

        $rows = $this->applyListFilters($query, $filters, ['order_number'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Order $order): array => [
            'Order' => $order->order_number,
            'Buyer' => $order->buyer?->name,
            'Status' => $order->status->value,
            'Total' => $order->grand_total.' '.$order->currency,
            'Placed' => $order->placed_at?->format('Y-m-d H:i') ?? 'n/a',
        ]);

        return $this->page('Orders', 'Buyer, supplier, and admin order visibility is scoped by role.', [], ['Order', 'Buyer', 'Status', 'Total', 'Placed'], $rows, filters: $filters);
    }

    public function marketplace(Request $request): Response
    {
        $filters = $this->filters($request);

        $rows = $this->applyListFilters(Product::query(), $filters, ['sku', 'name'], null)
            ->with('supplier')
            ->where('status', 'active')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Product $product): array => [
                'SKU' => $product->sku,
                'Product' => $product->name,
                'Supplier' => $product->supplier?->company_name,
                'Price' => $product->base_price,
                'MOQ' => $product->moq,
                'Available' => $product->availableStock(),
            ]);

        return $this->page('Marketplace', 'Active B2B products with supplier, MOQ, and stock context.', [], ['SKU', 'Product', 'Supplier', 'Price', 'MOQ', 'Available'], $rows, filters: $filters);
    }

    public function customers(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(CustomerStatus::class));

        $rows = $this->applyListFilters(Customer::query(), $filters, ['contact_name', 'company_name', 'email'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Customer $customer): array => [
            'Customer' => $customer->contact_name,
            'Company' => $customer->company_name,
            'Email' => $customer->email,
            'Stage' => $customer->lifecycle_stage->value,
            'Status' => $customer->status->value,
        ]);

        return $this->page('CRM Customers', 'Customer lifecycle, company profile, and status.', [], ['Customer', 'Company', 'Email', 'Stage', 'Status'], $rows, filters: $filters);
    }

    public function campaigns(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(CampaignStatus::class));

        $rows = $this->applyListFilters(Campaign::query()->withCount(['recipients', 'logs']), $filters, ['name'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Campaign $campaign): array => [
            'Campaign' => $campaign->name,
            'Type' => $campaign->type->value,
            'Status' => $campaign->status->value,
            'Recipients' => $campaign->recipients_count,
            'Logs' => $campaign->logs_count,
        ]);

        return $this->page('Campaigns', 'Marketing campaigns, recipient count, and delivery logs.', [], ['Campaign', 'Type', 'Status', 'Recipients', 'Logs'], $rows, filters: $filters);
    }

    public function socialCalendar(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(SocialPostStatus::class));

        $rows = $this->applyListFilters(SocialPost::query(), $filters, ['content'])
            ->orderBy('scheduled_at')
            ->limit(50)
            ->get()
            ->map(fn (SocialPost $post): array => [
            'Platform' => $post->platform->value,
            'Status' => $post->status->value,
            'Scheduled' => $post->scheduled_at?->format('Y-m-d H:i') ?? 'n/a',
            'Content' => str($post->content)->limit(80)->toString(),
            'Reach' => $post->reach_count,
            'Clicks' => $post->clicks_count,
        ]);

        return $this->page('Social Calendar', 'Scheduled posts and engagement placeholders.', [], ['Platform', 'Status', 'Scheduled', 'Content', 'Reach', 'Clicks'], $rows, filters: $filters);
    }

    public function workflowLogs(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(WorkflowLogStatus::class));

        $rows = $this->applyListFilters(WorkflowLog::query()->with('rule'), $filters, ['trigger_event', 'error'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (WorkflowLog $log): array => [
            'Rule' => $log->rule?->name ?? 'n/a',
            'Trigger' => $log->trigger_event,
            'Status' => $log->status->value,
            'Executed' => $log->executed_at?->format('Y-m-d H:i') ?? 'n/a',
            'Error' => $log->error ? str($log->error)->limit(80)->toString() : '',
        ]);

        return $this->page('Workflow Logs', 'Automation execution history with payload snapshots stored in the database.', [], ['Rule', 'Trigger', 'Status', 'Executed', 'Error'], $rows, filters: $filters);
    }

    public function supportTickets(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(TicketStatus::class));
        $query = SupportTicket::query()->with(['requester', 'supplier']);

        if ($request->user()->hasRole('buyer')) {
            $query->where('requester_id', $request->user()->id);
        }

        if ($request->user()->hasRole('supplier')) {
            $query->whereHas('supplier', fn (Builder $supplier) => $supplier->where('user_id', $request->user()->id));
        }

        $rows = $this->applyListFilters($query, $filters, ['ticket_number', 'subject', 'description'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (SupportTicket $ticket): array => [
                'Ticket' => $ticket->ticket_number,
                'Subject' => $ticket->subject,
                'Requester' => $ticket->requester?->name,
                'Supplier' => $ticket->supplier?->company_name,
                'Priority' => $ticket->priority->value,
                'Status' => $ticket->status->value,
                'Updated' => $ticket->last_message_at?->format('Y-m-d H:i') ?? 'n/a',
            ]);

        return $this->page('Support Tickets', 'Buyer and supplier support tickets with automated replies and supplier notifications.', [], ['Ticket', 'Subject', 'Requester', 'Supplier', 'Priority', 'Status', 'Updated'], $rows, filters: $filters);
    }

    private function page(string $title, string $description, array $metrics, array $columns, iterable $rows, string $emptyState = 'No records found.', ?array $filters = null): Response
    {
        return Inertia::render('Workspace/Index', [
            'workspace' => [
                'title' => $title,
                'description' => $description,
                'metrics' => $metrics,
                'columns' => $columns,
                'rows' => collect($rows)->values()->all(),
                'emptyState' => $emptyState,
                'filters' => $filters,
            ],
        ]);
    }

    /**
     * @param array<int, string> $statusOptions
     * @return array{search: string, status: string, statuses: array<int, string>}
     */
    private function filters(Request $request, array $statusOptions = []): array
    {
        $status = (string) $request->query('status', '');

        if ($status !== '' && ! in_array($status, $statusOptions, true)) {
            $status = '';
        }

        return [
            'search' => trim((string) $request->query('search', '')),
            'status' => $status,
            'statuses' => array_values($statusOptions),
        ];
    }

    /**
     * @param array{search: string, status: string, statuses: array<int, string>} $filters
     * @param array<int, string> $searchColumns
     */
    private function applyListFilters(Builder $query, array $filters, array $searchColumns, ?string $statusColumn = 'status'): Builder
    {
        if ($filters['search'] !== '') {
            $query->where(function (Builder $query) use ($filters, $searchColumns): void {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, 'like', '%'.$filters['search'].'%');
                }
            });
        }

        if ($statusColumn !== null && $filters['status'] !== '') {
            $query->where($statusColumn, $filters['status']);
        }

        return $query;
    }

    /**
     * @param class-string<BackedEnum> $enumClass
     * @return array<int, string>
     */
    private function enumValues(string $enumClass): array
    {
        return array_map(fn (BackedEnum $case): string => $case->value, $enumClass::cases());
    }
}

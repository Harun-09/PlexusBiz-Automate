<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Notifications\Models\Message;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\Audit\Models\AuditLog;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            ['label' => 'Audit Logs', 'value' => AuditLog::count()],
        ], ['Metric', 'Value'], [], 'Admin KPIs are shown above.');
    }

    public function auditLogs(Request $request): Response
    {
        $filters = $this->filters($request);

        $query = AuditLog::query()->with('actor');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $query) use ($search): void {
                $query->where('module_key', 'like', '%'.$search.'%')
                    ->orWhere('action', 'like', '%'.$search.'%')
                    ->orWhere('subject_label', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('actor', fn (Builder $actor) => $actor
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%'));
            });
        }

        $rows = $query
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (AuditLog $log): array => [
                'Action' => $this->labelForAuditValue($log->action),
                'Module' => $this->labelForAuditValue($log->module_key),
                'Actor' => $log->actor ? "{$log->actor->name} ({$log->actor->email})" : 'System',
                'Subject' => $log->subject_label ?: ($log->subject_type ? class_basename($log->subject_type) : 'n/a'),
                'Description' => $log->description ?? '',
                'Executed' => $log->created_at?->format('Y-m-d H:i') ?? 'n/a',
                'IP' => $log->ip_address ?? 'n/a',
            ]);

        return $this->page(
            'Audit Logs',
            'Critical admin and workflow changes with actor, subject, and request context.',
            [
                ['label' => 'Total Logs', 'value' => AuditLog::count()],
                ['label' => 'Today', 'value' => AuditLog::whereDate('created_at', today())->count()],
                ['label' => 'Admin Changes', 'value' => AuditLog::where('module_key', 'admin')->count()],
                ['label' => 'Workflow Changes', 'value' => AuditLog::where('module_key', 'workflow')->count()],
            ],
            ['Action', 'Module', 'Actor', 'Subject', 'Description', 'Executed', 'IP'],
            $rows,
            'No audit logs found.',
            $filters,
        );
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
        $canInitiatePayment = $request->user()->hasRole('buyer') || $request->user()->hasRole('admin');

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
            ->map(function (Order $order) use ($canInitiatePayment): array {
                return [
                    'Order' => $order->order_number,
                    'Buyer' => $order->buyer?->name,
                    'Status' => $order->status->value,
                    'Payment' => $this->orderPaymentSummary($order),
                    'Total' => $order->grand_total.' '.$order->currency,
                    'Placed' => $order->placed_at?->format('Y-m-d H:i') ?? 'n/a',
                    'Action' => $this->orderPaymentAction($order, $canInitiatePayment),
                ];
            });

        return $this->page('Orders', 'Buyer, supplier, and admin order visibility is scoped by role.', [], ['Order', 'Buyer', 'Status', 'Payment', 'Total', 'Placed', 'Action'], $rows, filters: $filters);
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
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $statusFilter = (string) $request->query('status', '');

        $statuses = $this->enumValues(SocialPostStatus::class);

        if ($statusFilter !== '' && ! in_array($statusFilter, $statuses, true)) {
            $statusFilter = '';
        }

        $startOfMonth = now()->setDate($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $query = SocialPost::query();

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $posts = $query
            ->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn (SocialPost $post): array => [
                'id' => $post->id,
                'platform' => $post->platform->value,
                'status' => $post->status->value,
                'content' => $post->content,
                'content_short' => str($post->content)->limit(60)->toString(),
                'scheduled_at' => $post->scheduled_at?->toISOString(),
                'scheduled_date' => $post->scheduled_at?->format('Y-m-d'),
                'scheduled_time' => $post->scheduled_at?->format('H:i'),
                'published_at' => $post->published_at?->format('Y-m-d H:i'),
                'likes' => $post->likes_count ?? 0,
                'comments' => $post->comments_count ?? 0,
                'shares' => $post->shares_count ?? 0,
                'reach' => $post->reach_count ?? 0,
                'clicks' => $post->clicks_count ?? 0,
                'failure_reason' => $post->failure_reason,
            ])
            ->all();

        return Inertia::render('Social/Calendar', [
            'posts' => $posts,
            'month' => $month,
            'year' => $year,
            'status' => $statusFilter,
            'statuses' => $statuses,
        ]);
    }

    public function workflowLogs(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(WorkflowLogStatus::class));

        $rows = $this->applyListFilters(WorkflowLog::query()->with('rule'), $filters, ['trigger_event', 'error'])
            ->latest()
            ->orderByDesc('id')
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

    public function notifications(Request $request): Response
    {
        $filters = $this->filters($request);
        $query = Message::query()->with(['sender', 'receiver']);

        if (! $request->user()->hasRole('admin')) {
            $query->where('receiver_id', $request->user()->id);
        }

        $rows = $this->applyListFilters($query, $filters, ['subject', 'body', 'channel'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Message $message): array => [
                'Subject' => $message->subject ?? 'System notification',
                'Channel' => $message->channel->value,
                'Status' => $message->status->value,
                'From' => $message->sender?->name ?? 'System',
                'To' => $message->receiver?->name ?? 'Broadcast',
                'Sent' => $message->sent_at?->format('Y-m-d H:i') ?? 'n/a',
            ]);

        return $this->page(
            'Notifications',
            'In-app, email, SMS, and system messages routed through the notifications domain.',
            [
                ['label' => 'Visible Messages', 'value' => $rows->count()],
                ['label' => 'Unread', 'value' => (clone $query)->whereNull('read_at')->count()],
            ],
            ['Subject', 'Channel', 'Status', 'From', 'To', 'Sent'],
            $rows,
            'No notifications found.',
            $filters,
        );
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

    private function orderPaymentSummary(Order $order): array
    {
        $status = $order->payment_status ?: PaymentStatus::Pending->value;
        $gateway = $order->payment_method ?: config('commerce.default_payment_gateway', 'stripe');

        return [
            'kind' => 'payment-summary',
            'status' => $status,
            'method' => $this->formatPaymentGateway($gateway),
        ];
    }

    private function orderPaymentAction(Order $order, bool $canInitiatePayment): array
    {
        if ($order->isPaid()) {
            $checkoutToken = trim((string) $order->checkout_token);

            if ($checkoutToken !== '') {
                return [
                    'kind' => 'link',
                    'label' => 'View receipt',
                    'href' => route('checkout.success', [
                        'orderNumber' => $order->order_number,
                        'access_token' => $checkoutToken,
                    ]),
                ];
            }

            return [
                'kind' => 'status',
                'label' => 'Paid',
                'status' => PaymentStatus::Completed->value,
            ];
        }

        if (! $canInitiatePayment) {
            return [
                'kind' => 'status',
                'label' => ucfirst($order->payment_status ?: PaymentStatus::Pending->value),
                'status' => $order->payment_status ?: PaymentStatus::Pending->value,
            ];
        }

        return [
            'kind' => 'payment-action',
            'label' => $order->payment_method ? 'Continue payment' : 'Pay now',
            'href' => route('payment.process', $order->order_number),
            'gateway' => $this->formatPaymentGateway($order->payment_method ?: config('commerce.default_payment_gateway', 'stripe')),
        ];
    }

    private function formatPaymentGateway(?string $gateway): string
    {
        $gateway = strtolower(trim((string) $gateway));

        return match ($gateway) {
            'stripe' => 'Stripe',
            'sslcommerz' => 'SSLCOMMERZ',
            '' => 'Stripe',
            default => ucwords(str_replace(['_', '-'], ' ', $gateway)),
        };
    }

    private function labelForAuditValue(string $value): string
    {
        return Str::headline(str_replace(['.', '_'], ' ', $value));
    }
}

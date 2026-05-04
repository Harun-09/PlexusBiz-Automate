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
use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Notifications\Models\Message;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\SupportFaqStatus;
use App\Domains\Support\Models\SupportFaq;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\AutomationRule;
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

        $rows = $this->applyListFilters(Campaign::query()->withCount(['recipients', 'logs', 'templates']), $filters, ['name'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Campaign $campaign): array => [
            'Campaign' => $campaign->name,
            'Type' => Str::headline($campaign->type->value),
            'Status' => $campaign->status->value,
            'Templates' => $campaign->templates_count,
            'Recipients' => $campaign->recipients_count,
            'Logs' => $campaign->logs_count,
        ]);

        return $this->page('Campaigns', 'Marketing campaigns, recipient count, and delivery logs.', [
            ['label' => 'Total Campaigns', 'value' => Campaign::count()],
            ['label' => 'Draft Campaigns', 'value' => Campaign::where('status', CampaignStatus::Draft->value)->count()],
            ['label' => 'Scheduled Campaigns', 'value' => Campaign::where('status', CampaignStatus::Scheduled->value)->count()],
            ['label' => 'Templates', 'value' => CampaignTemplate::count()],
        ], ['Campaign', 'Type', 'Status', 'Templates', 'Recipients', 'Logs'], $rows, filters: $filters, component: 'Marketing/Campaigns/Index');
    }

    public function socialPosts(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(SocialPostStatus::class));

        $query = SocialPost::query()->with(['account', 'campaign']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('content', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%")
                    ->orWhereHas('account', fn (Builder $account) => $account
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('handle', 'like', "%{$search}%"))
                    ->orWhereHas('campaign', fn (Builder $campaign) => $campaign
                        ->where('name', 'like', "%{$search}%"));
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->orderByDesc('scheduled_at')
            ->limit(50)
            ->get()
            ->map(fn (SocialPost $post): array => [
                'Post' => str($post->content)->limit(72)->toString(),
                'Platform' => Str::headline($post->platform->value),
                'Account' => $post->account?->name ?? 'n/a',
                'Campaign' => $post->campaign?->name ?? 'n/a',
                'Status' => $post->status->value,
                'Scheduled' => $post->scheduled_at?->format('Y-m-d H:i') ?? 'n/a',
                'Published' => $post->published_at?->format('Y-m-d H:i') ?? 'n/a',
                'Likes' => $post->likes_count ?? 0,
                'Comments' => $post->comments_count ?? 0,
                'Shares' => $post->shares_count ?? 0,
                'Reach' => $post->reach_count ?? 0,
                'Clicks' => $post->clicks_count ?? 0,
            ]);

        return $this->page('Social Posts', 'Scheduled content across Facebook and Instagram with engagement tracking.', [
            ['label' => 'Total Posts', 'value' => SocialPost::count()],
            ['label' => 'Scheduled', 'value' => SocialPost::where('status', SocialPostStatus::Scheduled->value)->count()],
            ['label' => 'Published', 'value' => SocialPost::where('status', SocialPostStatus::Published->value)->count()],
            ['label' => 'Failed', 'value' => SocialPost::where('status', SocialPostStatus::Failed->value)->count()],
        ], ['Post', 'Platform', 'Account', 'Campaign', 'Status', 'Scheduled', 'Published', 'Likes', 'Comments', 'Shares', 'Reach', 'Clicks'], $rows, filters: $filters, component: 'Social/Posts/Index');
    }

    public function socialAccounts(Request $request): Response
    {
        $filters = $this->filters($request, ['active', 'inactive']);

        $query = SocialAccount::query()->withCount('posts');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('handle', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (SocialAccount $account): array {
                $credentials = $account->credentials_json ?? [];

                return [
                    'Account' => $account->name,
                    'Platform' => Str::headline($account->platform->value),
                    'Handle' => $account->handle,
                    'Status' => (string) $account->status,
                    'Posts' => $account->posts_count,
                    'Mode' => is_array($credentials) ? (string) ($credentials['mode'] ?? 'n/a') : 'n/a',
                ];
            });

        return $this->page('Social Accounts', 'Publishing accounts for Facebook and Instagram scheduling.', [
            ['label' => 'Total Accounts', 'value' => SocialAccount::count()],
            ['label' => 'Active Accounts', 'value' => SocialAccount::where('status', 'active')->count()],
            ['label' => 'Facebook', 'value' => SocialAccount::where('platform', SocialPlatform::Facebook->value)->count()],
            ['label' => 'Instagram', 'value' => SocialAccount::where('platform', SocialPlatform::Instagram->value)->count()],
        ], ['Account', 'Platform', 'Handle', 'Status', 'Posts', 'Mode'], $rows, filters: $filters, component: 'Social/Accounts/Index');
    }

    public function campaignTemplates(Request $request): Response
    {
        $filters = $this->filters($request, ['active', 'inactive']);

        $query = CampaignTemplate::query()->with('campaign');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('template_key', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('campaign', fn (Builder $campaign) => $campaign
                        ->where('name', 'like', "%{$search}%"));
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (CampaignTemplate $template): array {
                return [
                    'Template' => $template->name,
                    'Key' => $template->template_key ?? 'n/a',
                    'Channel' => Str::headline($template->channel->value),
                    'Campaign' => $template->campaign?->name ?? 'Standalone',
                    'Subject' => $template->subject ?? '',
                    'Variables' => is_array($template->variables) ? implode(', ', $template->variables) : '',
                    'Status' => (string) $template->status,
                ];
            });

        return $this->page('Campaign Templates', 'Template-based email and SMS marketing assets.', [
            ['label' => 'Total Templates', 'value' => CampaignTemplate::count()],
            ['label' => 'Email Templates', 'value' => CampaignTemplate::where('channel', MessageChannel::Email->value)->count()],
            ['label' => 'SMS Templates', 'value' => CampaignTemplate::where('channel', MessageChannel::Sms->value)->count()],
            ['label' => 'System Templates', 'value' => CampaignTemplate::where('channel', MessageChannel::System->value)->count()],
        ], ['Template', 'Key', 'Channel', 'Campaign', 'Subject', 'Variables', 'Status'], $rows, filters: $filters, component: 'Marketing/Templates/Index');
    }

    public function workflowRules(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(AutomationRuleStatus::class));

        $query = AutomationRule::query()->withCount('logs');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('trigger_event', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->orderBy('priority')
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (AutomationRule $rule): array {
                return [
                    'Rule' => $rule->name,
                    'Trigger' => Str::headline(str_replace(['.', '_'], ' ', $rule->trigger_event)),
                    'Status' => $rule->status->value,
                    'Priority' => $rule->priority,
                    'Mode' => $rule->run_async ? 'async' : 'sync',
                    'Conditions' => $this->workflowConditionsSummary($rule->conditions_json ?? []),
                    'Actions' => $this->workflowActionsSummary($rule->actions_json ?? []),
                    'Runs' => $rule->logs_count,
                ];
            });

        return $this->page('Automation Rules', 'Rule-based IF condition THEN action workflows for orders, RFQs, and tickets.', [
            ['label' => 'Total Rules', 'value' => AutomationRule::count()],
            ['label' => 'Active Rules', 'value' => AutomationRule::where('status', AutomationRuleStatus::Active->value)->count()],
            ['label' => 'Async Rules', 'value' => AutomationRule::where('run_async', true)->count()],
            ['label' => 'Successful Logs', 'value' => WorkflowLog::where('status', WorkflowLogStatus::Success->value)->count()],
        ], ['Rule', 'Trigger', 'Status', 'Priority', 'Mode', 'Conditions', 'Actions', 'Runs'], $rows, filters: $filters, component: 'Workflow/Rules/Index');
    }

    public function supportFaqs(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(SupportFaqStatus::class));

        $query = SupportFaq::query();

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%")
                    ->orWhere('keywords_json', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->orderBy('priority')
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (SupportFaq $faq): array {
                return [
                    'Question' => $faq->question,
                    'Keywords' => is_array($faq->keywords_json) ? implode(', ', $faq->keywords_json) : '',
                    'Status' => $faq->status->value,
                    'Priority' => $faq->priority,
                    'Answer' => $faq->answer,
                ];
            });

        return $this->page('Support FAQ', 'Chatbot-ready answers used by customer and supplier support flows.', [
            ['label' => 'Total FAQs', 'value' => SupportFaq::count()],
            ['label' => 'Active FAQs', 'value' => SupportFaq::where('status', SupportFaqStatus::Active->value)->count()],
            ['label' => 'Priority 10', 'value' => SupportFaq::where('priority', 10)->count()],
            ['label' => 'Priority 20', 'value' => SupportFaq::where('priority', 20)->count()],
        ], ['Question', 'Keywords', 'Status', 'Priority', 'Answer'], $rows, filters: $filters, component: 'Support/Faq/Index');
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

        return $this->page('Workflow Logs', 'Automation execution history with payload snapshots stored in the database.', [
            ['label' => 'Total Logs', 'value' => WorkflowLog::count()],
            ['label' => 'Successful', 'value' => WorkflowLog::where('status', WorkflowLogStatus::Success->value)->count()],
            ['label' => 'Failed', 'value' => WorkflowLog::where('status', WorkflowLogStatus::Failed->value)->count()],
            ['label' => 'Running', 'value' => WorkflowLog::where('status', WorkflowLogStatus::Running->value)->count()],
        ], ['Rule', 'Trigger', 'Status', 'Executed', 'Error'], $rows, filters: $filters, component: 'Workflow/Logs/Index');
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

        return $this->page('Support Tickets', 'Buyer and supplier support tickets with automated replies and supplier notifications.', [
            ['label' => 'Total Tickets', 'value' => SupportTicket::count()],
            ['label' => 'Open', 'value' => SupportTicket::where('status', TicketStatus::Open->value)->count()],
            ['label' => 'Waiting Supplier', 'value' => SupportTicket::where('status', TicketStatus::WaitingSupplier->value)->count()],
            ['label' => 'Resolved', 'value' => SupportTicket::where('status', TicketStatus::Resolved->value)->count()],
        ], ['Ticket', 'Subject', 'Requester', 'Supplier', 'Priority', 'Status', 'Updated'], $rows, filters: $filters, component: 'Support/Tickets/Index');
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

    private function page(string $title, string $description, array $metrics, array $columns, iterable $rows, string $emptyState = 'No records found.', ?array $filters = null, string $component = 'Workspace/Index'): Response
    {
        return Inertia::render($component, [
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

    /**
     * @param array<int, array<string, mixed>> $conditions
     */
    private function workflowConditionsSummary(array $conditions): string
    {
        if ($conditions === []) {
            return 'No conditions';
        }

        return collect($conditions)
            ->map(function (array $condition): string {
                $field = (string) ($condition['field'] ?? 'field');
                $operator = (string) ($condition['operator'] ?? 'equals');
                $value = $condition['value'] ?? 'n/a';

                if (is_array($value)) {
                    $value = implode(', ', array_map('strval', $value));
                }

                $fieldLabel = Str::headline(str_replace(['.', '_'], ' ', $field));
                $operatorLabel = Str::headline(str_replace('_', ' ', $operator));

                return Str::limit("{$fieldLabel} {$operatorLabel} {$value}", 60);
            })
            ->implode(' | ');
    }

    /**
     * @param array<int, array<string, mixed>> $actions
     */
    private function workflowActionsSummary(array $actions): string
    {
        if ($actions === []) {
            return 'No actions';
        }

        return collect($actions)
            ->map(function (array $action): string {
                $type = (string) ($action['type'] ?? 'action');
                $config = is_array($action['config'] ?? null) ? $action['config'] : [];
                $message = (string) ($config['message'] ?? $config['subject'] ?? $config['url'] ?? '');

                $summary = $message !== '' ? Str::headline(str_replace('_', ' ', $type)).": {$message}" : Str::headline(str_replace('_', ' ', $type));

                return Str::limit($summary, 60);
            })
            ->implode(' | ');
    }
}

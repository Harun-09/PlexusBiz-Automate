<?php

namespace App\Http\Controllers\Crm;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Enums\LeadStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Models\CustomerSegment;
use App\Domains\CRM\Models\Interaction;
use App\Domains\CRM\Models\Lead;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\CustomerSegmentationService;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Rfq;
use App\Http\Controllers\Controller;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CrmController extends Controller
{
    public function __construct(
        private readonly CustomerProfileService $profiles,
        private readonly CustomerSegmentationService $segments,
    ) {
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('crm.customers.index');
    }

    public function customers(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(CustomerStatus::class));

        $query = Customer::query()
            ->with(['user'])
            ->withCount('orders')
            ->withSum('orders', 'grand_total')
            ->withMax('orders', 'placed_at');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('contact_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('business_type', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (Customer $customer): array {
                $summary = $this->profiles->purchaseSummary($customer);

                return [
                    'Customer' => $customer->contact_name,
                    'Company' => $customer->company_name,
                    'Business Type' => $customer->business_type ?: '-',
                    'Email' => $customer->email,
                    'Phone' => $customer->phone ?: '-',
                    'Stage' => $customer->lifecycle_stage->value,
                    'Status' => $customer->status->value,
                    'Orders' => (int) $customer->orders_count,
                    'Total Spent' => $this->formatMoney($summary['total_spent']),
                    'Last Order' => $this->formatDate($summary['last_order_at']),
                    'Action' => [
                        'kind' => 'link',
                        'label' => 'View profile',
                        'href' => route('crm.customers.show', $customer),
                    ],
                ];
            });

        return $this->page(
            'CRM Customers',
            'Customer registration and profiling with purchase history context.',
            [
                ['label' => 'Total Customers', 'value' => Customer::count()],
                ['label' => 'Active Profiles', 'value' => Customer::where('status', CustomerStatus::Active->value)->count()],
                ['label' => 'Repeat Customers', 'value' => Customer::where('lifecycle_stage', CustomerLifecycleStage::RepeatCustomer->value)->count()],
                ['label' => 'Profiles With Orders', 'value' => Customer::has('orders')->count()],
            ],
            ['Customer', 'Company', 'Business Type', 'Email', 'Phone', 'Stage', 'Status', 'Orders', 'Total Spent', 'Last Order', 'Action'],
            $rows,
            'No customer profiles found.',
            $filters,
            'CRM/Customers/Index',
        );
    }

    public function show(Request $request, Customer $customer): Response
    {
        $this->authorize('view', $customer);

        $customer->loadMissing([
            'user',
            'leads.assignedUser',
            'interactions.user',
            'orders.invoice',
            'orders.items.product',
        ]);

        $summary = $this->profiles->purchaseSummary($customer);

        return Inertia::render('CRM/Customers/Show', [
            'customer' => [
                'id' => $customer->id,
                'contact_name' => $customer->contact_name,
                'company_name' => $customer->company_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'business_type' => $customer->business_type,
                'address' => $customer->address ?? [],
                'status' => $customer->status->value,
                'lifecycle_stage' => $customer->lifecycle_stage->value,
                'tags' => $customer->tags ?? [],
                'notes' => $customer->notes,
                'last_activity_at' => $customer->last_activity_at?->toIso8601String(),
                'user' => $customer->user ? [
                    'id' => $customer->user->id,
                    'name' => $customer->user->name,
                    'email' => $customer->user->email,
                ] : null,
                'created_at' => $customer->created_at?->toIso8601String(),
            ],
            'summary' => [
                'orders_count' => $summary['orders_count'],
                'total_spent' => $this->formatMoney($summary['total_spent']),
                'last_order_at' => $this->formatDate($summary['last_order_at']),
                'leads_count' => $customer->leads->count(),
                'interactions_count' => $customer->interactions->count(),
            ],
            'recentOrders' => $customer->orders
                ->sortByDesc('placed_at')
                ->take(8)
                ->values()
                ->map(function (Order $order): array {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status->value,
                        'payment_status' => $order->payment_status ?? PaymentStatus::Pending->value,
                        'currency' => $order->currency,
                        'total' => $this->formatMoney($order->grand_total, $order->currency),
                        'placed_at' => $this->formatDate($order->placed_at),
                        'invoice_number' => $order->invoice?->invoice_number,
                        'action' => $this->invoiceAction($order),
                    ];
                })
                ->all(),
            'recentLeads' => $customer->leads
                ->sortByDesc('created_at')
                ->take(6)
                ->values()
                ->map(function (Lead $lead): array {
                    return [
                        'id' => $lead->id,
                        'company_name' => $lead->company_name,
                        'contact_name' => $lead->contact_name,
                        'email' => $lead->email,
                        'phone' => $lead->phone,
                        'source' => $lead->source,
                        'status' => $lead->status->value,
                        'value' => $this->formatMoney($lead->value),
                        'assigned_to' => $lead->assignedUser?->name ?? 'unassigned',
                        'next_follow_up_at' => $this->formatDate($lead->next_follow_up_at),
                    ];
                })
                ->all(),
            'recentInteractions' => $customer->interactions
                ->sortByDesc('occurred_at')
                ->take(8)
                ->values()
                ->map(function (Interaction $interaction): array {
                    return [
                        'id' => $interaction->id,
                        'type' => $interaction->type->value,
                        'direction' => $interaction->direction ?: 'internal',
                        'summary' => $interaction->summary ?? '',
                        'actor' => $interaction->user?->name ?? 'System',
                        'related' => $this->relatedLabel($interaction),
                        'occurred_at' => $this->formatDate($interaction->occurred_at),
                    ];
                })
                ->all(),
        ]);
    }

    public function purchases(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(OrderStatus::class));

        $query = Order::query()->with(['buyer', 'customer', 'invoice'])->withCount('items');

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('buyer', fn (Builder $buyer) => $buyer
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('customer', fn (Builder $customer) => $customer
                        ->where('company_name', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->latest('placed_at')
            ->limit(50)
            ->get()
            ->map(function (Order $order): array {
                return [
                    'Order' => $order->order_number,
                    'Customer' => $order->customer?->contact_name ?: $order->customer?->company_name ?: '-',
                    'Buyer' => $order->buyer?->name ?: '-',
                    'Items' => (int) $order->items_count,
                    'Status' => $order->status->value,
                    'Payment' => $this->orderPaymentSummary($order),
                    'Total' => $this->formatMoney($order->grand_total, $order->currency),
                    'Placed' => $this->formatDate($order->placed_at),
                    'Invoice' => $order->invoice?->invoice_number ?: 'n/a',
                    'Action' => $this->invoiceAction($order),
                ];
            });

        return $this->page(
            'Purchase History',
            'Customer purchase tracking across confirmed, pending, and completed orders.',
            [
                ['label' => 'Total Orders', 'value' => Order::count()],
                ['label' => 'Completed Orders', 'value' => Order::where('status', OrderStatus::Completed->value)->count()],
                ['label' => 'Pending Orders', 'value' => Order::where('status', OrderStatus::Pending->value)->count()],
                ['label' => 'Revenue', 'value' => $this->formatMoney(Order::where('payment_status', PaymentStatus::Completed->value)->sum('grand_total'))],
            ],
            ['Order', 'Customer', 'Buyer', 'Items', 'Status', 'Payment', 'Total', 'Placed', 'Invoice', 'Action'],
            $rows,
            'No purchase history found.',
            $filters,
            'CRM/Purchases/Index',
        );
    }

    public function segments(Request $request): Response
    {
        $filters = $this->filters($request, ['active', 'inactive']);

        $query = CustomerSegment::query();

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $segments = $query->latest()->limit(50)->get();

        $rows = $segments->map(function (CustomerSegment $segment): array {
            $filtersJson = is_array($segment->filters_json) ? $segment->filters_json : [];
            $audience = $this->segments->query($filtersJson)->count();

            return [
                'Segment' => $segment->name,
                'Slug' => $segment->slug,
                'Status' => (string) $segment->status,
                'Audience' => number_format($audience),
                'Criteria' => $this->segmentCriteriaSummary($filtersJson),
                'Description' => $segment->description ?: '',
                'Updated' => $this->formatDate($segment->updated_at),
            ];
        });

        return $this->page(
            'Customer Segments',
            'Basic audience segmentation for campaign targeting and account prioritization.',
            [
                ['label' => 'Saved Segments', 'value' => CustomerSegment::count()],
                ['label' => 'Active Segments', 'value' => CustomerSegment::where('status', 'active')->count()],
                ['label' => 'Segmented Audience', 'value' => number_format((int) $segments->sum(fn (CustomerSegment $segment) => $this->segments->query(is_array($segment->filters_json) ? $segment->filters_json : [])->count()))],
                ['label' => 'Customers', 'value' => Customer::count()],
            ],
            ['Segment', 'Slug', 'Status', 'Audience', 'Criteria', 'Description', 'Updated'],
            $rows,
            'No customer segments found.',
            $filters,
            'CRM/Segments/Index',
        );
    }

    public function leads(Request $request): Response
    {
        $filters = $this->filters($request, $this->enumValues(LeadStatus::class));

        $query = Lead::query()->with(['assignedUser', 'customer']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $rows = $query
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (Lead $lead): array {
                return [
                    'Lead' => $lead->contact_name,
                    'Company' => $lead->company_name,
                    'Email' => $lead->email,
                    'Phone' => $lead->phone ?: '-',
                    'Source' => $lead->source ?: 'n/a',
                    'Assigned' => $lead->assignedUser?->name ?? 'unassigned',
                    'Value' => $this->formatMoney($lead->value),
                    'Status' => $lead->status->value,
                    'Follow Up' => $this->formatDate($lead->next_follow_up_at),
                    'Action' => [
                        [
                            'kind' => 'link',
                            'label' => 'Edit',
                            'href' => route('crm.leads.edit', $lead),
                            'variant' => 'secondary',
                        ],
                        [
                            'kind' => 'link',
                            'label' => 'Delete',
                            'href' => route('crm.leads.destroy', $lead),
                            'method' => 'delete',
                            'variant' => 'danger',
                            'confirm' => 'Delete this lead?',
                        ],
                    ],
                ];
            });

        return $this->page(
            'Lead Management',
            'Lead tracking, assignment, and follow-up planning for the CRM team.',
            [
                ['label' => 'Total Leads', 'value' => Lead::count()],
                ['label' => 'Qualified', 'value' => Lead::where('status', LeadStatus::Qualified->value)->count()],
                ['label' => 'Converted', 'value' => Lead::where('status', LeadStatus::Converted->value)->count()],
                ['label' => 'Pipeline Value', 'value' => $this->formatMoney(Lead::sum('value'))],
            ],
            ['Lead', 'Company', 'Email', 'Phone', 'Source', 'Assigned', 'Value', 'Status', 'Follow Up', 'Action'],
            $rows,
            'No leads found.',
            $filters,
            'CRM/Leads/Index',
        );
    }

    public function interactions(Request $request): Response
    {
        $filters = $this->filters($request);

        $query = Interaction::query()->with(['customer', 'user', 'related']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('summary', 'like', "%{$search}%")
                    ->orWhere('direction', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn (Builder $customer) => $customer
                        ->where('contact_name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn (Builder $user) => $user
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $rows = $query
            ->latest('occurred_at')
            ->limit(50)
            ->get()
            ->map(function (Interaction $interaction): array {
                return [
                    'Customer' => $interaction->customer?->contact_name ?: $interaction->customer?->company_name ?: '-',
                    'Type' => $interaction->type->value,
                    'Direction' => $interaction->direction ?: 'internal',
                    'Actor' => $interaction->user?->name ?? 'System',
                    'Summary' => $interaction->summary ?: '',
                    'Related' => $this->relatedLabel($interaction),
                    'Occurred' => $this->formatDate($interaction->occurred_at),
                ];
            });

        return $this->page(
            'Interaction History',
            'Messages, orders, RFQ events, notes, and other CRM activity in one timeline.',
            [
                ['label' => 'Total Interactions', 'value' => Interaction::count()],
                ['label' => 'Order Events', 'value' => Interaction::where('type', InteractionType::Order->value)->count()],
                ['label' => 'RFQ Events', 'value' => Interaction::where('type', InteractionType::Rfq->value)->count()],
                ['label' => 'Inbounds', 'value' => Interaction::where('direction', 'inbound')->count()],
            ],
            ['Customer', 'Type', 'Direction', 'Actor', 'Summary', 'Related', 'Occurred'],
            $rows,
            'No CRM interactions found.',
            $filters,
            'CRM/Interactions/Index',
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
     * @param class-string<BackedEnum> $enumClass
     * @return array<int, string>
     */
    private function enumValues(string $enumClass): array
    {
        return array_map(fn (BackedEnum $case): string => $case->value, $enumClass::cases());
    }

    private function formatMoney(int|float|string|null $value, ?string $currency = null): string
    {
        $currency ??= config('commerce.currency', 'BDT');

        return sprintf('%s %s', $currency, number_format((float) $value, 2, '.', ','));
    }

    private function formatDate(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i');
        }

        $value = trim((string) $value);

        if ($value === '') {
            return 'n/a';
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return 'n/a';
        }

        return date('Y-m-d H:i', $timestamp);
    }

    private function segmentCriteriaSummary(array $filters): string
    {
        if ($filters === []) {
            return 'No saved criteria';
        }

        $parts = [];

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $label = match ($key) {
                'status' => 'status',
                'lifecycle_stage' => 'stage',
                'tags' => 'tags',
                'min_purchase_count' => 'min orders',
                'min_total_spent' => 'min spend',
                'last_activity_before' => 'inactive before',
                default => Str::headline((string) $key),
            };

            if (is_array($value)) {
                $value = implode(', ', array_map('strval', $value));
            }

            $parts[] = sprintf('%s: %s', $label, $value);
        }

        return $parts !== [] ? implode(' · ', $parts) : 'No saved criteria';
    }

    private function relatedLabel(Interaction $interaction): string
    {
        if ($interaction->related instanceof Order) {
            return 'Order '.$interaction->related->order_number;
        }

        if ($interaction->related instanceof Rfq) {
            return 'RFQ '.$interaction->related->rfq_number;
        }

        return $interaction->related_type ? class_basename($interaction->related_type) : 'n/a';
    }

    private function invoiceAction(Order $order): array
    {
        if ($order->invoice) {
            return [
                'kind' => 'link',
                'label' => 'Open invoice',
                'href' => route('invoices.show', $order->invoice),
            ];
        }

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
            'label' => ucfirst($order->payment_status ?: PaymentStatus::Pending->value),
            'status' => $order->payment_status ?: PaymentStatus::Pending->value,
        ];
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
}

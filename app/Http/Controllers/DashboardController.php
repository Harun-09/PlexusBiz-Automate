<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\SupplierOrder;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\Audit\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $role = RoleName::tryFrom($user->roles()->value('name') ?? RoleName::Buyer->value) ?? RoleName::Buyer;

        return Inertia::render('Dashboard', [
            'dashboard' => [
                'role' => [
                    'key' => $role->value,
                    'label' => $role->label(),
                ],
                'status' => $user->status?->value ?? 'active',
                'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                'cards' => $this->cardsFor($role, $user),
                'quickLinks' => $this->quickLinksFor($role, $user),
            ],
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function cardsFor(RoleName $role, User $user): array
    {
        return match ($role) {
            RoleName::Admin => $this->adminCards(),
            RoleName::Supplier => $this->supplierCards($user),
            RoleName::MarketingManager => $this->marketingCards(),
            RoleName::WorkflowManager => $this->workflowManagerCards(),
            RoleName::Buyer => $this->buyerCards($user),
        };
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function adminCards(): array
    {
        $totalOrders = Order::count();
        $revenue = Order::where('payment_status', PaymentStatus::Completed->value)->sum('grand_total');
        $pendingOrders = Order::where('status', OrderStatus::Pending->value)->count();
        $pendingPayments = Order::where('payment_status', PaymentStatus::Pending->value)->count();
        $pendingApplications = User::where('status', UserStatus::Pending->value)->count();
        $customers = Customer::buyerAccounts()->count();

        return [
            $this->statCard(
                'Total Orders',
                $this->formatCount($totalOrders),
                'All orders placed across the platform.',
                'blue',
            ),
            $this->statCard(
                'Revenue',
                $this->formatMoney($revenue),
                'Completed payments recorded in the system.',
                'emerald',
            ),
            $this->statCard(
                'Pending Orders',
                $this->formatCount($pendingOrders),
                'Orders waiting for confirmation or fulfillment.',
                'amber',
            ),
            $this->statCard(
                'Pending Payments',
                $this->formatCount($pendingPayments),
                'Orders that still need payment completion.',
                'rose',
            ),
            $this->statCard(
                'Pending Applications',
                $this->formatCount($pendingApplications),
                'Role requests waiting for admin review.',
                'amber',
            ),
            $this->statCard(
                'Customers',
                $this->formatCount($customers),
                'CRM customer profiles available across the platform.',
                'blue',
            ),
            $this->statCard(
                'Audit Logs',
                $this->formatCount(AuditLog::count()),
                'Critical admin and workflow changes recorded so far.',
                'slate',
            ),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buyerCards(User $user): array
    {
        $orders = Order::where('buyer_id', $user->id);
        $totalOrders = (clone $orders)->count();
        $spent = (clone $orders)->where('payment_status', PaymentStatus::Completed->value)->sum('grand_total');
        $pendingPayments = (clone $orders)->whereIn('payment_status', [
            PaymentStatus::Pending->value,
            PaymentStatus::Processing->value,
        ])->count();
        $openTickets = SupportTicket::where('requester_id', $user->id)
            ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
            ->count();

        return [
            $this->statCard(
                'My Orders',
                $this->formatCount($totalOrders),
                'Orders placed by this buyer account.',
                'blue',
            ),
            $this->statCard(
                'Total Spent',
                $this->formatMoney($spent),
                'Completed orders only, scoped to your account.',
                'emerald',
            ),
            $this->statCard(
                'Pending Payments',
                $this->formatCount($pendingPayments),
                'Order is created, but payment still needs gateway confirmation or retry.',
                'amber',
            ),
            $this->statCard(
                'Open Support Tickets',
                $this->formatCount($openTickets),
                'Active support requests linked to your user.',
                'rose',
            ),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function supplierCards(User $user): array
    {
        $supplier = $user->supplier;
        $supplierId = $supplier?->id;

        if (! $supplierId) {
            return [
                $this->statCard('My Products', '0', 'No supplier profile is linked to this user.', 'blue'),
                $this->statCard('Active Listings', '0', 'No active catalog items yet.', 'emerald'),
                $this->statCard('Total Sales', 'BDT 0.00', 'No completed sales are linked to this supplier.', 'amber'),
                $this->statCard('Wallet Balance', 'BDT 0.00', 'Escrow earnings ready for withdrawal.', 'blue'),
                $this->statCard('Stock On Hand', '0', 'No inventory is linked to this supplier yet.', 'slate'),
                $this->statCard('Customers Served', '0', 'No buyer accounts have ordered from this supplier yet.', 'rose'),
                $this->statCard('Pending Fulfillment', '0', 'No supplier orders waiting right now.', 'amber'),
                $this->statCard('Open Tickets', '0', 'No support tickets are attached to this supplier.', 'rose'),
            ];
        }

        $products = Product::where('supplier_id', $supplierId);
        $lowStockProducts = (clone $products)->get()->filter(fn (Product $product): bool => $product->isLowStock())->count();
        $completedSales = OrderItem::query()
            ->where('supplier_id', $supplierId)
            ->whereHas('order', fn ($order) => $order->where('payment_status', PaymentStatus::Completed->value))
            ->sum('total');
        $stockOnHand = (int) Product::query()
            ->where('supplier_id', $supplierId)
            ->get()
            ->sum(fn (Product $product): int => $product->availableStock());
        $customersServed = Order::query()
            ->where('payment_status', PaymentStatus::Completed->value)
            ->whereHas('items', fn ($items) => $items->where('supplier_id', $supplierId))
            ->distinct('buyer_id')
            ->count('buyer_id');
        $openTickets = SupportTicket::where('supplier_id', $supplierId)
            ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
            ->count();

        $pendingFulfillment = SupplierOrder::query()
            ->where('supplier_id', $supplierId)
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Confirmed->value,
                OrderStatus::Processing->value,
            ])
            ->count();

        if (! $supplier->isApproved()) {
            $applicationStatus = match ($supplier->status) {
                SupplierStatus::Rejected => 'Rejected',
                SupplierStatus::Suspended => 'Suspended',
                default => 'Pending Review',
            };

            return [
                $this->statCard(
                    'Application Status',
                    $applicationStatus,
                    'Your supplier profile is not approved yet.',
                    'amber',
                ),
                $this->statCard(
                    'My Products',
                    $this->formatCount((clone $products)->count()),
                    'Products submitted under this pending supplier profile.',
                    'blue',
                ),
                $this->statCard(
                    'Low Stock Alerts',
                    $this->formatCount($lowStockProducts),
                    'Products at or below the warning threshold.',
                    'rose',
                ),
                $this->statCard(
                    'Total Sales',
                    $this->formatMoney($completedSales),
                    'Completed order value from this supplier\'s sold items.',
                    'amber',
                ),
                $this->statCard(
                    'Wallet Balance',
                    $this->formatMoney($supplier->wallet_balance),
                    'Escrow earnings ready for withdrawal.',
                    'blue',
                ),
                $this->statCard(
                    'Customers Served',
                    $this->formatCount($customersServed),
                    'Unique buyer accounts linked to this supplier.',
                    'rose',
                ),
                $this->statCard(
                    'Pending Fulfillment',
                    $this->formatCount($pendingFulfillment),
                    'Supplier orders waiting on action.',
                    'amber',
                ),
                $this->statCard(
                    'Open Tickets',
                    $this->formatCount($openTickets),
                    'Supplier support requests needing attention.',
                    'rose',
                ),
            ];
        }

        return [
            $this->statCard(
                'My Products',
                $this->formatCount((clone $products)->count()),
                'All catalog items owned by this supplier.',
                'blue',
            ),
            $this->statCard(
                'Active Listings',
                $this->formatCount((clone $products)->where('status', ProductStatus::Active->value)->count()),
                'Products currently available for buyers.',
                'emerald',
            ),
            $this->statCard(
                'Low Stock Alerts',
                $this->formatCount($lowStockProducts),
                'Products at or below the warning threshold.',
                'rose',
            ),
            $this->statCard(
                'Total Sales',
                $this->formatMoney($completedSales),
                'Completed order value from this supplier\'s sold items.',
                'amber',
            ),
            $this->statCard(
                'Wallet Balance',
                $this->formatMoney($supplier->wallet_balance),
                'Escrow earnings ready for withdrawal.',
                'blue',
            ),
            $this->statCard(
                'Stock On Hand',
                $this->formatCount($stockOnHand),
                'Available inventory across this supplier catalog.',
                'slate',
            ),
            $this->statCard(
                'Customers Served',
                $this->formatCount($customersServed),
                'Unique buyer accounts that purchased from this supplier.',
                'rose',
            ),
            $this->statCard(
                'Pending Fulfillment',
                $this->formatCount($pendingFulfillment),
                'Orders with supplier items still in motion.',
                'amber',
            ),
            $this->statCard(
                'Open Tickets',
                $this->formatCount($openTickets),
                'Supplier support requests needing attention.',
                'rose',
            ),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function marketingCards(): array
    {
        $campaigns = Campaign::count();
        $scheduledPosts = SocialPost::where('status', SocialPostStatus::Scheduled->value)->count();
        $publishedPosts = SocialPost::where('status', SocialPostStatus::Published->value)->count();
        $failedAutomations = WorkflowLog::where('status', WorkflowLogStatus::Failed->value)->count();

        return [
            $this->statCard(
                'Email Campaigns',
                $this->formatCount($campaigns),
                'Email campaigns available in the workspace.',
                'blue',
            ),
            $this->statCard(
                'Scheduled Posts',
                $this->formatCount($scheduledPosts),
                'Social posts queued for future publishing.',
                'amber',
            ),
            $this->statCard(
                'Published Posts',
                $this->formatCount($publishedPosts),
                'Posts already live across platforms.',
                'emerald',
            ),
            $this->statCard(
                'Failed Automations',
                $this->formatCount($failedAutomations),
                'Workflow runs that need a retry or fix.',
                'rose',
            ),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function workflowManagerCards(): array
    {
        $activeRules = AutomationRule::where('status', 'active')->count();
        $totalRuns = WorkflowLog::count();
        $failedRuns = WorkflowLog::where('status', WorkflowLogStatus::Failed->value)->count();
        $runningRuns = WorkflowLog::where('status', WorkflowLogStatus::Running->value)->count();

        return [
            $this->statCard(
                'Active Rules',
                $this->formatCount($activeRules),
                'Automation rules currently eligible to run.',
                'blue',
            ),
            $this->statCard(
                'Workflow Runs',
                $this->formatCount($totalRuns),
                'Execution logs captured with payload snapshots.',
                'emerald',
            ),
            $this->statCard(
                'Failed Runs',
                $this->formatCount($failedRuns),
                'Automation executions that need review.',
                'rose',
            ),
            $this->statCard(
                'Running',
                $this->formatCount($runningRuns),
                'Queued or in-progress automation executions.',
                'amber',
            ),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statCard(string $label, string $value, string $description, string $tone): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'description' => $description,
            'tone' => $tone,
        ];
    }

    private function formatCount(int|float|string $value): string
    {
        return number_format((float) $value, 0, '.', ',');
    }

    private function formatMoney(int|float|string $value): string
    {
        return 'BDT '.number_format((float) $value, 2, '.', ',');
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function quickLinksFor(RoleName $role, User $user): array
    {
        return match ($role) {
            RoleName::Admin => [
                ['label' => 'Users', 'href' => '/admin/users'],
                ['label' => 'Customers', 'href' => '/crm/customers'],
                ['label' => 'Suppliers', 'href' => '/admin/suppliers'],
                ['label' => 'Supplier Orders', 'href' => '/commerce/supplier-orders'],
                ['label' => 'Module Settings', 'href' => '/admin/modules'],
                ['label' => 'Audit Logs', 'href' => '/admin/audit-logs'],
                ['label' => 'Workflow Logs', 'href' => '/workflow/logs'],
                ['label' => 'Trash (Recycle Bin)', 'href' => '/admin/trash'],
            ],
            RoleName::Supplier => [
                ['label' => 'Products', 'href' => '/commerce/products'],
                ['label' => 'Inventory', 'href' => '/commerce/products'],
                ['label' => 'Orders', 'href' => '/commerce/orders'],
                ['label' => 'Supplier Orders', 'href' => '/commerce/supplier-orders'],
                ...($user->supplier?->status === SupplierStatus::Approved ? [
                    ['label' => 'Add Product', 'href' => '/commerce/products/create'],
                ] : []),
            ],
            RoleName::MarketingManager => [
                ['label' => 'Email Campaigns', 'href' => '/marketing/campaigns'],
                ['label' => 'Social Campaigns', 'href' => '/social/posts'],
                ['label' => 'Social Calendar', 'href' => '/social/calendar'],
                ['label' => 'Workflow Logs', 'href' => '/workflow/logs'],
            ],
            RoleName::WorkflowManager => [
                ['label' => 'Workflow Logs', 'href' => '/workflow/logs'],
                ['label' => 'Failed Logs', 'href' => '/workflow/logs?status=failed'],
                ['label' => 'Dashboard', 'href' => '/dashboard'],
            ],
            RoleName::Buyer => [
                ['label' => 'Marketplace', 'href' => '/marketplace'],
                ['label' => 'Cart', 'href' => '/cart'],
                ['label' => 'Support', 'href' => '/support/tickets'],
            ],
        };
    }
}

<?php

namespace App\Http\Controllers;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Enums\RoleName;
use App\Models\User;
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
                'quickLinks' => $this->quickLinksFor($role),
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
        $pendingOrders = (clone $orders)->where('status', OrderStatus::Pending->value)->count();
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
                'Pending Orders',
                $this->formatCount($pendingOrders),
                'Orders still waiting on action.',
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
        $supplierId = $user->supplier?->id;

        if (! $supplierId) {
            return [
                $this->statCard('My Products', '0', 'No supplier profile is linked to this user.', 'blue'),
                $this->statCard('Active Listings', '0', 'No active catalog items yet.', 'emerald'),
                $this->statCard('Pending Fulfillment', '0', 'No supplier orders waiting right now.', 'amber'),
                $this->statCard('Open Tickets', '0', 'No support tickets are attached to this supplier.', 'rose'),
            ];
        }

        $products = Product::where('supplier_id', $supplierId);
        $supplierOrders = Order::query()->whereHas('items', fn ($items) => $items->where('supplier_id', $supplierId));
        $openTickets = SupportTicket::where('supplier_id', $supplierId)
            ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
            ->count();

        $pendingFulfillment = Order::query()
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Confirmed->value,
                OrderStatus::Processing->value,
            ])
            ->whereHas('items', fn ($items) => $items->where('supplier_id', $supplierId))
            ->count();

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
                'Campaigns',
                $this->formatCount($campaigns),
                'Marketing campaigns available in the workspace.',
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
    private function quickLinksFor(RoleName $role): array
    {
        return match ($role) {
            RoleName::Admin => [
                ['label' => 'Users', 'href' => '/admin/users'],
                ['label' => 'Suppliers', 'href' => '/admin/suppliers'],
                ['label' => 'Module Settings', 'href' => '/settings/modules'],
                ['label' => 'Workflow Logs', 'href' => '/workflow/logs'],
            ],
            RoleName::Supplier => [
                ['label' => 'Products', 'href' => '/commerce/products'],
                ['label' => 'Inventory', 'href' => '/commerce/inventory'],
                ['label' => 'Orders', 'href' => '/commerce/orders'],
            ],
            RoleName::MarketingManager => [
                ['label' => 'Campaigns', 'href' => '/marketing/campaigns'],
                ['label' => 'Social Calendar', 'href' => '/social/calendar'],
                ['label' => 'Automation Rules', 'href' => '/workflow/rules'],
            ],
            RoleName::Buyer => [
                ['label' => 'Marketplace', 'href' => '/marketplace'],
                ['label' => 'Cart', 'href' => '/commerce/cart'],
                ['label' => 'Support', 'href' => '/support/tickets'],
            ],
        };
    }
}

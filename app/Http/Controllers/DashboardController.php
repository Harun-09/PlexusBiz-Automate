<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
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
                'cards' => $this->cardsFor($role),
                'quickLinks' => $this->quickLinksFor($role),
            ],
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function cardsFor(RoleName $role): array
    {
        return match ($role) {
            RoleName::Admin => [
                ['label' => 'Platform Control', 'value' => 'Full', 'description' => 'Users, suppliers, modules, automation, orders, and settings.'],
                ['label' => 'Approval Queue', 'value' => 'Ready', 'description' => 'Supplier onboarding and access changes route through admin review.'],
                ['label' => 'Audit Surface', 'value' => 'Enabled', 'description' => 'Critical actions will be tracked through the audit log module.'],
            ],
            RoleName::Supplier => [
                ['label' => 'Catalog Ownership', 'value' => 'Scoped', 'description' => 'Supplier access is limited to owned products, inventory, orders, and RFQs.'],
                ['label' => 'Order Flow', 'value' => 'Assigned', 'description' => 'Supplier notifications will surface order and fulfillment work.'],
                ['label' => 'Support Access', 'value' => 'Open', 'description' => 'Supplier support messages stay tied to assigned business records.'],
            ],
            RoleName::MarketingManager => [
                ['label' => 'Campaign Control', 'value' => 'Active', 'description' => 'Campaigns, templates, social posts, and automation rules are available.'],
                ['label' => 'Workflow Visibility', 'value' => 'Enabled', 'description' => 'Automation logs and failed executions are part of this workspace.'],
                ['label' => 'Provider Mode', 'value' => 'Mockable', 'description' => 'Email, SMS, Facebook, and Instagram providers stay adapter-driven.'],
            ],
            RoleName::Buyer => [
                ['label' => 'Marketplace', 'value' => 'Available', 'description' => 'Catalog browsing, cart, checkout, invoices, and support are buyer scoped.'],
                ['label' => 'Account Status', 'value' => 'Active', 'description' => 'Buyer registration receives the buyer role automatically.'],
                ['label' => 'Support Flow', 'value' => 'Ready', 'description' => 'Tickets and chatbot-ready support endpoints will attach to buyer context.'],
            ],
        };
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

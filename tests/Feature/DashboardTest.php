<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Models\SupplierOrder;
use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Enums\SupportChannel;
use App\Domains\Support\Enums\TicketPriority;
use App\Domains\Support\Enums\TicketStatus;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowActionType;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_live_platform_stats(): void
    {
        $this->seed(RbacSeeder::class);
        $this->seedSharedCommerceData();

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->where('dashboard.role.key', 'admin')
                ->where('dashboard.cards.0.label', 'Total Orders')
                ->where('dashboard.cards.0.value', '3')
                ->where('dashboard.cards.1.label', 'Revenue')
                ->where('dashboard.cards.1.value', 'BDT 350.50')
                ->where('dashboard.cards.2.label', 'Pending Orders')
                ->where('dashboard.cards.2.value', '1')
                ->where('dashboard.cards.3.label', 'Pending Payments')
                ->where('dashboard.cards.3.value', '1'));
    }

    public function test_buyer_dashboard_scopes_live_stats_to_the_logged_in_user(): void
    {
        $this->seed(RbacSeeder::class);
        $this->seedSharedCommerceData();

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();

        $this->actingAs($buyer)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->where('dashboard.role.key', 'buyer')
                ->where('dashboard.cards.0.label', 'My Orders')
                ->where('dashboard.cards.0.value', '2')
                ->where('dashboard.cards.1.label', 'Total Spent')
                ->where('dashboard.cards.1.value', 'BDT 150.50')
                ->where('dashboard.cards.2.label', 'Pending Orders')
                ->where('dashboard.cards.2.value', '1')
                ->where('dashboard.cards.3.label', 'Open Support Tickets')
                ->where('dashboard.cards.3.value', '1'));
    }

    public function test_supplier_dashboard_shows_inventory_and_fulfillment_stats(): void
    {
        $this->seed(RbacSeeder::class);

        $supplierUser = User::where('email', 'supplier@plexus.test')->firstOrFail();
        $supplier = Supplier::create([
            'user_id' => $supplierUser->id,
            'company_name' => 'Plexus Supply Co',
            'slug' => 'plexus-supply-co',
            'status' => SupplierStatus::Approved->value,
            'contact_email' => 'supplier@plexus.test',
            'approved_at' => now(),
        ]);

        $activeProduct = Product::create([
            'supplier_id' => $supplier->id,
            'sku' => 'PX-SUP-001',
            'name' => 'Active Product',
            'slug' => 'active-product',
            'description' => 'Active product for the supplier dashboard test.',
            'base_price' => 1200,
            'moq' => 1,
            'stock_quantity' => 15,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active->value,
            'published_at' => now(),
        ]);

        Product::create([
            'supplier_id' => $supplier->id,
            'sku' => 'PX-SUP-002',
            'name' => 'Draft Product',
            'slug' => 'draft-product',
            'description' => 'Inactive product for the supplier dashboard test.',
            'base_price' => 800,
            'moq' => 1,
            'stock_quantity' => 10,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Inactive->value,
            'published_at' => now(),
        ]);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'order_number' => 'ORD-SUPPLIER-001',
            'status' => OrderStatus::Processing->value,
            'subtotal' => '1200.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '1200.00',
            'currency' => 'BDT',
            'placed_at' => now(),
            'payment_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $activeProduct->id,
            'supplier_id' => $supplier->id,
            'product_name' => $activeProduct->name,
            'sku' => $activeProduct->sku,
            'quantity' => 1,
            'unit_price' => '1200.00',
            'total' => '1200.00',
            'status' => OrderStatus::Processing->value,
        ]);

        SupplierOrder::create([
            'order_id' => $order->id,
            'supplier_id' => $supplier->id,
            'supplier_order_number' => 'SO-SUPPLIER-001',
            'status' => OrderStatus::Processing->value,
            'subtotal' => '1200.00',
            'grand_total' => '1200.00',
            'currency' => 'BDT',
            'placed_at' => now(),
        ]);

        SupportTicket::create([
            'ticket_number' => 'TKT-SUPPLIER-001',
            'requester_id' => $buyer->id,
            'supplier_id' => $supplier->id,
            'channel' => SupportChannel::Web->value,
            'subject' => 'Supplier dashboard ticket',
            'description' => 'Open ticket for supplier dashboard coverage.',
            'priority' => TicketPriority::Normal->value,
            'status' => TicketStatus::Open->value,
            'last_message_at' => now(),
        ]);

        $this->actingAs($supplierUser)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->where('dashboard.role.key', 'supplier')
                ->where('dashboard.cards.0.label', 'My Products')
                ->where('dashboard.cards.0.value', '2')
                ->where('dashboard.cards.1.label', 'Active Listings')
                ->where('dashboard.cards.1.value', '1')
                ->where('dashboard.cards.2.label', 'Low Stock Alerts')
                ->where('dashboard.cards.2.value', '1')
                ->where('dashboard.cards.6.label', 'Pending Fulfillment')
                ->where('dashboard.cards.6.value', '1')
                ->where('dashboard.cards.7.label', 'Open Tickets')
                ->where('dashboard.cards.7.value', '1'));
    }

    public function test_marketing_dashboard_shows_live_campaign_stats(): void
    {
        $this->seed(RbacSeeder::class);

        $marketingUser = User::where('email', 'marketing@plexus.test')->firstOrFail();

        Campaign::create([
            'created_by' => $marketingUser->id,
            'name' => 'Launch Campaign',
            'slug' => 'launch-campaign',
            'type' => CampaignType::Email->value,
            'status' => CampaignStatus::Scheduled->value,
            'scheduled_at' => now()->addHour(),
        ]);

        SocialPost::create([
            'platform' => SocialPlatform::Facebook->value,
            'content' => 'Scheduled post',
            'status' => SocialPostStatus::Scheduled->value,
            'scheduled_at' => now()->addHour(),
        ]);

        SocialPost::create([
            'platform' => SocialPlatform::Instagram->value,
            'content' => 'Published post',
            'status' => SocialPostStatus::Published->value,
            'published_at' => now(),
        ]);

        WorkflowLog::create([
            'trigger_event' => 'order.created',
            'payload' => ['order_number' => 'ORD-MKT-001'],
            'status' => WorkflowLogStatus::Failed->value,
            'error' => 'Webhook timeout',
            'executed_at' => now(),
        ]);

        $this->actingAs($marketingUser)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->where('dashboard.role.key', 'marketing_manager')
                ->where('dashboard.cards.0.label', 'Campaigns')
                ->where('dashboard.cards.0.value', '1')
                ->where('dashboard.cards.1.label', 'Scheduled Posts')
                ->where('dashboard.cards.1.value', '1')
                ->where('dashboard.cards.2.label', 'Published Posts')
                ->where('dashboard.cards.2.value', '1')
                ->where('dashboard.cards.3.label', 'Failed Automations')
                ->where('dashboard.cards.3.value', '1'));
    }

    public function test_workflow_manager_dashboard_shows_automation_stats(): void
    {
        $this->seed(RbacSeeder::class);

        $workflowUser = User::where('email', 'workflow@plexus.test')->firstOrFail();

        AutomationRule::create([
            'name' => 'Workflow dashboard rule',
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'conditions_json' => [
                ['field' => 'order.grand_total', 'operator' => 'greater_than', 'value' => 100],
            ],
            'actions_json' => [
                ['type' => WorkflowActionType::CreateNotification->value, 'config' => []],
            ],
            'status' => AutomationRuleStatus::Active->value,
            'priority' => 1,
            'run_async' => false,
        ]);

        WorkflowLog::create([
            'trigger_event' => WorkflowTriggerEvent::OrderPlaced->value,
            'payload' => ['order' => ['id' => 1]],
            'status' => WorkflowLogStatus::Failed->value,
            'error' => 'Mock failure',
            'executed_at' => now(),
        ]);

        $this->actingAs($workflowUser)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->where('dashboard.role.key', 'workflow_manager')
                ->where('dashboard.cards.0.label', 'Active Rules')
                ->where('dashboard.cards.0.value', '1')
                ->where('dashboard.cards.1.label', 'Workflow Runs')
                ->where('dashboard.cards.1.value', '1')
                ->where('dashboard.cards.2.label', 'Failed Runs')
                ->where('dashboard.cards.2.value', '1'));
    }

    private function seedSharedCommerceData(): void
    {
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $otherBuyer = User::factory()->create([
            'name' => 'Other Buyer',
            'email' => 'other-buyer@example.com',
        ]);

        Order::create([
            'buyer_id' => $buyer->id,
            'order_number' => 'ORD-BUYER-001',
            'status' => OrderStatus::Pending->value,
            'subtotal' => '75.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '75.00',
            'currency' => 'BDT',
            'placed_at' => now()->subDay(),
            'payment_status' => 'pending',
        ]);

        Order::create([
            'buyer_id' => $buyer->id,
            'order_number' => 'ORD-BUYER-002',
            'status' => OrderStatus::Completed->value,
            'subtotal' => '150.50',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '150.50',
            'currency' => 'BDT',
            'placed_at' => now()->subHours(2),
            'payment_status' => 'completed',
        ]);

        Order::create([
            'buyer_id' => $otherBuyer->id,
            'order_number' => 'ORD-OTHER-001',
            'status' => OrderStatus::Completed->value,
            'subtotal' => '200.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '200.00',
            'currency' => 'BDT',
            'placed_at' => now()->subHours(4),
            'payment_status' => 'completed',
        ]);

        SupportTicket::create([
            'ticket_number' => 'TKT-BUYER-001',
            'requester_id' => $buyer->id,
            'channel' => SupportChannel::Web->value,
            'subject' => 'Open buyer ticket',
            'description' => 'Buyer support ticket kept open for dashboard stats.',
            'priority' => TicketPriority::Normal->value,
            'status' => TicketStatus::Open->value,
            'last_message_at' => now()->subHour(),
        ]);

        SupportTicket::create([
            'ticket_number' => 'TKT-BUYER-002',
            'requester_id' => $buyer->id,
            'channel' => SupportChannel::Web->value,
            'subject' => 'Resolved buyer ticket',
            'description' => 'Resolved support ticket excluded from open count.',
            'priority' => TicketPriority::Normal->value,
            'status' => TicketStatus::Resolved->value,
            'resolved_at' => now()->subMinutes(30),
            'last_message_at' => now()->subMinutes(40),
        ]);
    }
}

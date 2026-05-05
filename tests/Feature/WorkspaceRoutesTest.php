<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WorkspaceRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_workspace_is_role_protected(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Admin/Control/Index'));

        $this->actingAs($admin)
            ->get('/admin/customers')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Admin/Customers/Index'));

        $this->actingAs($admin)
            ->get('/admin/audit-logs')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Admin/AuditLogs/Index'));

        $this->actingAs($buyer)->get('/admin')->assertForbidden();
    }

    public function test_role_workspaces_render_for_assigned_users(): void
    {
        $this->seed(RbacSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = User::where('email', 'supplier@plexus.test')->firstOrFail();
        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        $workflow = User::where('email', 'workflow@plexus.test')->firstOrFail();

        $this->actingAs($buyer)
            ->get('/marketplace')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Marketplace/Index'));

        $this->actingAs($buyer)->get('/support/tickets')->assertOk();

        $this->actingAs($buyer)
            ->get('/notifications')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Notifications/Index'));

        $this->actingAs($supplier)
            ->get('/commerce/products')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Commerce/Products/Index'));

        $this->actingAs($supplier)
            ->get('/commerce/orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Commerce/Orders/Index'));

        $this->actingAs($marketing)->get('/marketing/campaigns')->assertOk();
        $this->actingAs($marketing)->get('/social/campaigns')->assertRedirect('/social/posts');
        $this->actingAs($marketing)->get('/workflow/logs')->assertOk();
        $this->actingAs($workflow)->get('/workflow/logs')->assertOk();
    }

    public function test_pending_suppliers_do_not_receive_product_action_columns(): void
    {
        $this->seed(RbacSeeder::class);

        $supplierUser = User::factory()->create([
            'name' => 'Pending Supplier',
            'email' => 'pending.supplier@example.com',
            'account_type' => 'supplier',
        ]);

        $supplierUser->assignRole('supplier');

        $supplier = Supplier::factory()->create([
            'user_id' => $supplierUser->id,
            'status' => SupplierStatus::Pending,
            'approved_at' => null,
        ]);

        Product::factory()->create([
            'supplier_id' => $supplier->id,
        ]);

        $this->actingAs($supplierUser)
            ->get('/commerce/products')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Commerce/Products/Index')
                ->where('workspace.columns', ['SKU', 'Product', 'Supplier', 'Stock', 'MOQ', 'Status'])
                ->has('workspace.rows', 1));
    }

    public function test_workspace_tables_support_search_and_status_filters(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/users?search=buyer&status=active')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Users/Index')
                ->where('filters.search', 'buyer')
                ->where('filters.status', 'active')
                ->has('users.data', 2));
    }

    public function test_orders_workspace_exposes_payment_action_for_unpaid_buyer_orders(): void
    {
        $this->seed(RbacSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();

        Order::create([
            'buyer_id' => $buyer->id,
            'order_number' => 'ORD-'.Str::upper(Str::random(10)),
            'status' => OrderStatus::Pending,
            'subtotal' => '149.99',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '149.99',
            'currency' => 'BDT',
            'placed_at' => now(),
            'checkout_token' => null,
            'payment_method' => null,
            'payment_status' => PaymentStatus::Pending->value,
            'transaction_id' => null,
        ]);

        $this->actingAs($buyer)
            ->get('/commerce/orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Commerce/Orders/Index')
                ->where('workspace.columns.3', 'Payment')
                ->where('workspace.columns.6', 'Action')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Payment.kind', 'payment-summary')
                ->where('workspace.rows.0.Payment.status', PaymentStatus::Pending->value)
                ->where('workspace.rows.0.Payment.method', 'Stripe')
                ->where('workspace.rows.0.Action.kind', 'payment-action')
                ->where('workspace.rows.0.Action.label', 'Pay now'));
    }
}

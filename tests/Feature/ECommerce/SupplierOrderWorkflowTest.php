<?php

namespace Tests\Feature\ECommerce;

use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Models\SupplierOrder;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\WorkflowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupplierOrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_orders_page_exposes_status_actions_and_transitions(): void
    {
        $this->seed([RbacSeeder::class, WorkflowSeeder::class]);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $supplierUser = User::factory()->create([
            'name' => 'Supplier User',
            'email' => 'supplier-flow@plexus.test',
        ]);
        $supplierUser->assignRole(RoleName::Supplier->value);

        $supplier = Supplier::create([
            'user_id' => $supplierUser->id,
            'company_name' => 'Flow Wholesale',
            'slug' => 'flow-wholesale',
            'status' => SupplierStatus::Approved->value,
            'contact_email' => $supplierUser->email,
            'phone' => '01710000000',
            'tax_number' => 'TIN-FLOW-001',
            'address' => ['country' => 'Bangladesh'],
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        $buyer = User::factory()->create([
            'name' => 'Buyer User',
            'email' => 'buyer-flow@plexus.test',
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'customer_id' => null,
            'order_number' => 'PO-FLOW-001',
            'status' => OrderStatus::Pending,
            'subtotal' => '3200.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '3200.00',
            'currency' => 'BDT',
            'payment_status' => PaymentStatus::Completed->value,
            'placed_at' => now(),
        ]);

        $supplierOrder = SupplierOrder::create([
            'order_id' => $order->id,
            'supplier_id' => $supplier->id,
            'supplier_order_number' => 'SO-FLOW-001',
            'status' => OrderStatus::Pending,
            'subtotal' => '3200.00',
            'grand_total' => '3200.00',
            'currency' => 'BDT',
            'placed_at' => now()->subMinutes(15),
        ]);

        $this->actingAs($supplierUser)
            ->get('/commerce/supplier-orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Commerce/SupplierOrders/Index')
                ->where('workspace.title', 'Supplier Orders')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Status', 'pending')
                ->where('workspace.rows.0.Action.kind', 'post-action')
                ->where('workspace.rows.0.Action.label', 'Confirm order'));

        $this->actingAs($supplierUser)
            ->post("/commerce/supplier-orders/{$supplierOrder->id}/status/confirmed")
            ->assertRedirect('/commerce/supplier-orders');

        $supplierOrder->refresh();

        $this->assertSame(OrderStatus::Confirmed, $supplierOrder->status);
        $this->assertNotNull($supplierOrder->confirmed_at);
        $this->assertDatabaseHas('messages', [
            'receiver_id' => $buyer->id,
            'subject' => 'Your PlexusBiz order has been confirmed',
        ]);

        $statusLogs = WorkflowLog::where('trigger_event', WorkflowTriggerEvent::OrderStatusChanged->value)->get();

        $this->assertSame(4, $statusLogs->count());
        $this->assertSame(
            1,
            $statusLogs->filter(fn (WorkflowLog $log): bool => $log->status === WorkflowLogStatus::Success)->count()
        );
        $successfulLog = $statusLogs->first(fn (WorkflowLog $log): bool => $log->status === WorkflowLogStatus::Success);

        $this->assertSame(
            'confirmed',
            $successfulLog->payload['order']['status']
        );

        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $this->putJson('/api/v1/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('updated_count', 1);

        $this->getJson('/api/v1/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->actingAs($supplierUser)
            ->post("/commerce/supplier-orders/{$supplierOrder->id}/status/shipped")
            ->assertRedirect('/commerce/supplier-orders');

        $supplierOrder->refresh();

        $this->assertSame(OrderStatus::Shipped, $supplierOrder->status);
        $this->assertNotNull($supplierOrder->shipped_at);

        $this->actingAs($supplierUser)
            ->post("/commerce/supplier-orders/{$supplierOrder->id}/status/completed")
            ->assertRedirect('/commerce/supplier-orders');

        $supplierOrder->refresh();

        $this->assertSame(OrderStatus::Completed, $supplierOrder->status);
        $this->assertNotNull($supplierOrder->completed_at);
    }
}

<?php

namespace Tests\Feature\CRM;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Services\InteractionLogger;
use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\CRMSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CrmModuleRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_module_pages_render_for_admin_and_marketing_manager(): void
    {
        $this->seed(RbacSeeder::class);
        $this->seed(CRMSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $customer = Customer::where('user_id', $buyer->id)->firstOrFail();
        $supplierUser = User::factory()->create([
            'name' => 'Supplier Linked User',
            'email' => 'supplier-linked@example.test',
        ]);
        $supplierUser->assignRole(RoleName::Supplier->value);

        Customer::create([
            'user_id' => $supplierUser->id,
            'company_name' => 'Supplier Linked Company',
            'contact_name' => 'Supplier Linked User',
            'email' => 'supplier-linked@example.test',
            'status' => 'active',
            'lifecycle_stage' => 'customer',
            'tags' => [],
            'last_activity_at' => now(),
        ]);

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'customer_id' => $customer->id,
            'order_number' => 'PO-CRM-001',
            'status' => OrderStatus::Completed,
            'subtotal' => '1250.00',
            'tax_total' => '0.00',
            'shipping_total' => '0.00',
            'discount_total' => '0.00',
            'grand_total' => '1250.00',
            'currency' => 'BDT',
            'placed_at' => now(),
            'checkout_token' => null,
            'payment_method' => 'stripe',
            'payment_status' => PaymentStatus::Completed->value,
            'transaction_id' => 'txn_crm_001',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-CRM-001',
            'status' => InvoiceStatus::Issued,
            'subtotal' => '1250.00',
            'tax_total' => '0.00',
            'total' => '1250.00',
            'issued_at' => now(),
            'due_at' => now()->addDays(14),
        ]);

        $orderInteraction = app(InteractionLogger::class)->record(
            customer: $customer,
            type: InteractionType::Order,
            summary: 'Customer completed a test CRM order.',
            related: $order,
            payload: ['order_number' => $order->order_number],
            actor: $admin,
            direction: 'outbound',
        );

        $orderInteraction->forceFill([
            'occurred_at' => now()->addMinute(),
        ])->save();

        $customer->forceFill([
            'last_activity_at' => $orderInteraction->occurred_at,
        ])->save();

        $this->actingAs($admin)
            ->get('/crm')
            ->assertRedirect('/crm/customers');

        $this->actingAs($admin)
            ->get('/crm/customers')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Customers/Index')
                ->where('workspace.title', 'CRM Customers')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Customer', $customer->contact_name)
                ->where('workspace.rows.0.Action.0.kind', 'link')
                ->where('workspace.rows.0.Action.0.label', 'View profile')
                ->where('workspace.rows.0.Action.1.kind', 'link')
                ->where('workspace.rows.0.Action.1.label', 'Edit'));

        $this->actingAs($admin)
            ->get("/crm/customers/{$customer->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Customers/Show')
                ->where('customer.contact_name', $customer->contact_name)
                ->where('summary.orders_count', 1)
                ->where('summary.leads_count', 1)
                ->where('summary.interactions_count', 2)
                ->has('recentOrders', 1)
                ->where('recentOrders.0.order_number', 'PO-CRM-001')
                ->where('recentOrders.0.action.kind', 'link')
                ->has('recentLeads', 1)
                ->where('recentLeads.0.status', 'qualified')
                ->has('recentInteractions', 2)
                ->where('recentInteractions.0.type', 'order'));

        $this->actingAs($admin)
            ->get("/crm/customers/{$customer->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Customers/Edit')
                ->where('customer.contact_name', $customer->contact_name)
                ->where('summary.orders_count', 1));

        $this->actingAs($admin)
            ->put("/crm/customers/{$customer->id}", [
                'contact_name' => 'Buyer CRM Updated',
                'company_name' => 'CRM Updated Company',
                'email' => 'buyer.crm.updated@example.test',
                'phone' => '+8801800000000',
                'business_type' => 'Wholesale distributor',
                'status' => 'active',
                'lifecycle_stage' => 'repeat_customer',
                'tags' => 'priority, key-account',
                'notes' => 'Updated from CRM edit screen.',
                'address_line1' => 'House 22',
                'address_line2' => 'Road 8',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1207',
                'country' => 'Bangladesh',
            ])
            ->assertRedirect("/crm/customers/{$customer->id}");

        $customer->refresh();
        $buyer->refresh();

        $this->assertSame('Buyer CRM Updated', $customer->contact_name);
        $this->assertSame('CRM Updated Company', $customer->company_name);
        $this->assertSame('buyer.crm.updated@example.test', $customer->email);
        $this->assertSame(['priority', 'key-account'], $customer->tags);
        $this->assertSame('House 22', $customer->address['line_1']);
        $this->assertSame('buyer.crm.updated@example.test', $buyer->email);

        $this->actingAs($admin)
            ->get('/crm/purchases')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Purchases/Index')
                ->where('workspace.title', 'Purchase History')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Order', 'PO-CRM-001')
                ->where('workspace.rows.0.Invoice', $invoice->invoice_number));

        $this->actingAs($admin)
            ->get('/crm/segments')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Segments/Index')
                ->where('workspace.title', 'Customer Segments')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Segment', 'Priority Wholesale'));

        $this->actingAs($marketing)
            ->get('/crm/leads')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Leads/Index')
                ->where('workspace.title', 'Lead Management')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Status', 'qualified'));

        $this->actingAs($marketing)
            ->get('/crm/interactions')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('CRM/Interactions/Index')
                ->where('workspace.title', 'Interaction History')
                ->has('workspace.metrics', 6)
                ->where('workspace.metrics.0.label', 'Total Interactions')
                ->where('workspace.metrics.1.label', 'Order Events')
                ->where('workspace.metrics.2.label', 'RFQ Events')
                ->where('workspace.metrics.3.label', 'Message Events')
                ->where('workspace.metrics.4.label', 'Support Ticket Events')
                ->where('workspace.metrics.5.label', 'Inbounds')
                ->has('workspace.rows', 2)
                ->where('workspace.rows.0.Type', 'order'));
    }
}

<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Product;
use App\Domains\Tax\Models\TaxConfiguration;
use App\Domains\Tax\Events\VatRecorded;
use App\Domains\Core\Models\OutboxMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MushakGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_placed_generates_mushak_and_outbox_event()
    {
        $user = \App\Models\User::factory()->create();
        $supplier = \App\Domains\ECommerce\Models\Supplier::factory()->create(['user_id' => $user->id]);
        $customer = \App\Domains\CRM\Models\Customer::factory()->create(['user_id' => $user->id]);
        TaxConfiguration::forceCreate(['region' => 'BD', 'tax_rate' => 10.00]);
        $product = Product::forceCreate(['name' => 'Prod', 'sku' => 'PROD-01', 'slug' => 'prod-01', 'base_price' => 100, 'moq' => 1, 'supplier_id' => $supplier->id]);
        
        $order = Order::forceCreate(['order_number' => 'ORD-03', 'buyer_id' => $user->id, 'customer_id' => $customer->id, 'status' => 'pending', 'grand_total' => 100]);
        OrderItem::forceCreate([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'supplier_id' => $supplier->id,
            'unit_price' => 100,
            'quantity' => 1,
            'total' => 100,
        ]);

        // Dispatch the event
        event(new OrderPlaced($order));

        // Check TaxInvoice exists
        $this->assertDatabaseHas('tax_invoices', [
            'order_id' => $order->id,
        ]);

        // Check MushakDocument exists
        $this->assertDatabaseHas('mushak_documents', [
            'form_type' => '6.3',
            'total_vat_amount' => 10.00,
        ]);

        // Check Outbox message for VatRecorded
        $this->assertDatabaseHas('outbox_messages', [
            'event_type' => VatRecorded::class,
        ]);
    }
}

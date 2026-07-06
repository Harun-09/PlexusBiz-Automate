<?php

namespace Tests\Feature;

use App\Domains\Procurement\Models\PurchaseOrder;
use App\Domains\Procurement\Models\PurchaseOrderItem;
use App\Domains\Procurement\Models\GoodsReceiptNote;
use App\Domains\Procurement\Models\SupplierInvoice;
use App\Domains\Procurement\Services\ThreeWayMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreeWayMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_matching_succeeds_for_perfect_match()
    {
        $po = PurchaseOrder::create(['po_number' => 'PO-001', 'supplier_id' => 1, 'total_amount' => 1000]);
        $item = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => 1,
            'unit_price' => 100,
            'quantity' => 10,
            'total' => 1000,
        ]);

        $grn = GoodsReceiptNote::create([
            'grn_number' => 'GRN-001',
            'purchase_order_id' => $po->id,
            'receipt_date' => now(),
            'received_by' => 'Store Manager',
            'received_quantities' => [$item->id => 10], // 10 received
        ]);

        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-001',
            'purchase_order_id' => $po->id,
            'total_amount' => 1000,
            'invoiced_quantities' => [$item->id => 10], // 10 invoiced
        ]);

        $service = new ThreeWayMatchingService();
        
        $this->assertTrue($service->match($po, $grn, $invoice));
        
        // Assert invoice status is updated to matched
        $this->assertEquals('matched', $invoice->fresh()->status);
        
        // Check if Mushak 6.1 is created
        $this->assertDatabaseHas('mushak_records', [
            'book_type' => '6.1',
            'reference_id' => $po->id,
        ]);
    }

    public function test_matching_fails_when_invoiced_quantity_exceeds_received()
    {
        $po = PurchaseOrder::create(['po_number' => 'PO-002', 'supplier_id' => 1, 'total_amount' => 1000]);
        $item = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => 1,
            'unit_price' => 100,
            'quantity' => 10,
            'total' => 1000,
        ]);

        $grn = GoodsReceiptNote::create([
            'grn_number' => 'GRN-002',
            'purchase_order_id' => $po->id,
            'receipt_date' => now(),
            'received_by' => 'Store Manager',
            'received_quantities' => [$item->id => 8], // 8 received
        ]);

        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-002',
            'purchase_order_id' => $po->id,
            'total_amount' => 1000,
            'invoiced_quantities' => [$item->id => 10], // 10 invoiced
        ]);

        $service = new ThreeWayMatchingService();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Invoiced quantity for item {$item->id} exceeds received quantity.");
        
        $service->match($po, $grn, $invoice);
    }
}

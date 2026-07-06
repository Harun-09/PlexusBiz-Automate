<?php

namespace Tests\Feature;

use App\Domains\Procurement\Models\PurchaseOrder;
use App\Domains\Procurement\Models\PurchaseOrderItem;
use App\Domains\Procurement\Models\LandedCost;
use App\Domains\Procurement\Services\LandedCostEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandedCostTest extends TestCase
{
    use RefreshDatabase;

    public function test_landed_cost_allocation_by_value()
    {
        $po = PurchaseOrder::create(['po_number' => 'PO-LC-001', 'supplier_id' => 1, 'total_amount' => 1000]);
        
        // Item 1: 40% of value
        $item1 = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => 1,
            'unit_price' => 100,
            'quantity' => 4,
            'total' => 400,
        ]);
        
        // Item 2: 60% of value
        $item2 = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => 2,
            'unit_price' => 150,
            'quantity' => 4,
            'total' => 600,
        ]);

        $landedCost = LandedCost::create([
            'purchase_order_id' => $po->id,
            'freight_cost' => 100,
            'customs_duty' => 200,
            'port_handling' => 100,
        ]); // total overhead = 400

        $engine = new LandedCostEngine();
        $allocations = $engine->allocateCostsByValue($po, $landedCost);

        // 40% of 400 = 160
        // Item 1 Base total = 400. Total Cost = 560. Unit Cost = 560 / 4 = 140
        $this->assertEquals(160.00, $allocations[0]['allocated_overhead']);
        $this->assertEquals(140.00, $allocations[0]['unit_landed_cost']);

        // 60% of 400 = 240
        // Item 2 Base total = 600. Total Cost = 840. Unit Cost = 840 / 4 = 210
        $this->assertEquals(240.00, $allocations[1]['allocated_overhead']);
        $this->assertEquals(210.00, $allocations[1]['unit_landed_cost']);
    }
}

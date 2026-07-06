<?php

namespace App\Domains\Procurement\Services;

use App\Domains\Procurement\Models\PurchaseOrder;
use App\Domains\Procurement\Models\LandedCost;

class LandedCostEngine
{
    /**
     * Calculate and allocate total landed costs across PO line items based on 'value'.
     */
    public function allocateCostsByValue(PurchaseOrder $po, LandedCost $landedCost)
    {
        $totalOverhead = $landedCost->freight_cost + $landedCost->insurance_cost + $landedCost->customs_duty + $landedCost->port_handling;
        $landedCost->total_landed_cost = $totalOverhead;
        $landedCost->save();

        if ($po->total_amount <= 0) {
            return []; // Avoid division by zero
        }

        $allocatedItems = [];

        foreach ($po->items as $item) {
            // Value ratio: (item_total / po_total)
            $ratio = $item->total / $po->total_amount;
            
            // Allocate overhead based on ratio
            $itemOverhead = $totalOverhead * $ratio;
            
            // Total cost per item = base item total + allocated overhead
            $itemTotalCost = $item->total + $itemOverhead;

            // Unit Landed Cost
            $unitLandedCost = $itemTotalCost / max($item->quantity, 1);

            $allocatedItems[] = [
                'po_item_id' => $item->id,
                'product_id' => $item->product_id,
                'base_unit_price' => $item->unit_price,
                'allocated_overhead' => round($itemOverhead, 2),
                'unit_landed_cost' => round($unitLandedCost, 2),
            ];
        }

        return $allocatedItems;
    }
}

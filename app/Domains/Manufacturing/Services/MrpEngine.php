<?php

namespace App\Domains\Manufacturing\Services;

use App\Domains\Manufacturing\Models\BillOfMaterial;
use App\Domains\Inventory\Models\InventoryBatch;
use App\Domains\Procurement\Models\PurchaseOrder;
use App\Domains\Procurement\Models\PurchaseOrderItem;

class MrpEngine
{
    /**
     * Explode the BOM to calculate dependent demand and generate Draft POs if shortages exist.
     */
    public function calculateDependentDemand(int $bomId, int $targetQuantity)
    {
        $bom = BillOfMaterial::with('items')->findOrFail($bomId);
        $shortages = [];
        
        foreach ($bom->items as $item) {
            $requiredQuantity = $item->quantity_required * $targetQuantity;
            
            // Check inventory
            $availableQuantity = InventoryBatch::where('product_id', $item->raw_material_product_id)->sum('available_quantity');
            
            if ($availableQuantity < $requiredQuantity) {
                $shortageAmount = $requiredQuantity - $availableQuantity;
                $shortages[] = [
                    'product_id' => $item->raw_material_product_id,
                    'shortage_quantity' => $shortageAmount
                ];
            }
        }

        // If requested, auto-generate a draft PO
        if (count($shortages) > 0) {
            $this->generateDraftPurchaseOrder($shortages);
        }

        return $shortages;
    }

    private function generateDraftPurchaseOrder(array $shortages)
    {
        $po = PurchaseOrder::create([
            'po_number' => 'AUTO-MRP-' . now()->format('YmdHis'),
            'supplier_id' => 1,
            'status' => 'pending',
            'total_amount' => 0
        ]);

        foreach ($shortages as $shortage) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $shortage['product_id'],
                'quantity' => $shortage['shortage_quantity'],
                'unit_price' => 0,
                'total' => 0
            ]);
        }
        
        return $po;
    }
}

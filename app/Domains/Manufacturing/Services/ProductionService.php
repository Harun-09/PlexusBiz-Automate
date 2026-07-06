<?php

namespace App\Domains\Manufacturing\Services;

use App\Domains\Manufacturing\Models\BillOfMaterial;
use App\Domains\Manufacturing\Models\ProductionOrder;
use App\Domains\Inventory\Models\InventoryBatch;
use App\Domains\Inventory\Models\StockLocation;
use App\Domains\Inventory\Models\StockMovement;
use Exception;

class ProductionService
{
    /**
     * Complete a Production Order:
     * 1. Deduct raw materials from inventory (FIFO)
     * 2. Create finished goods in inventory
     */
    public function completeProductionOrder(ProductionOrder $order)
    {
        if ($order->status === 'completed') {
            throw new Exception("Production Order #{$order->order_number} is already completed.");
        }

        $bom = BillOfMaterial::with('items')->findOrFail($order->bill_of_material_id);

        // Step 1: Deduct raw materials
        foreach ($bom->items as $item) {
            $requiredQty = $item->quantity_required * $order->target_quantity;
            $this->deductRawMaterial($item->raw_material_product_id, $requiredQty);
        }

        // Step 2: Create finished goods batch
        $location = StockLocation::first(); // Use first available location
        if (!$location) {
            throw new Exception("No stock locations available to store finished goods.");
        }

        $finishedBatch = InventoryBatch::create([
            'product_id' => $bom->product_id,
            'stock_location_id' => $location->id,
            'batch_number' => 'MFG-' . $order->order_number . '-' . now()->format('Ymd'),
            'initial_quantity' => $order->target_quantity * $bom->produced_quantity,
            'available_quantity' => $order->target_quantity * $bom->produced_quantity,
            'unit_cost' => 0, // Would be calculated from raw material costs in a real scenario
        ]);

        StockMovement::create([
            'inventory_batch_id' => $finishedBatch->id,
            'type' => 'in',
            'quantity' => $finishedBatch->initial_quantity,
            'reference_type' => 'ProductionOrder',
            'reference_id' => $order->id,
        ]);

        // Step 3: Mark order as completed
        $order->status = 'completed';
        $order->end_date = now();
        $order->save();

        return $order;
    }

    private function deductRawMaterial(int $productId, float $quantity)
    {
        $batches = InventoryBatch::where('product_id', $productId)
            ->where('available_quantity', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO
            ->get();

        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $deduct = min($batch->available_quantity, $remaining);
            $batch->available_quantity -= $deduct;
            $batch->save();

            StockMovement::create([
                'inventory_batch_id' => $batch->id,
                'type' => 'out',
                'quantity' => $deduct,
                'reference_type' => 'ProductionConsumption',
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new Exception("Insufficient raw material (Product ID: {$productId}). Short by {$remaining} units.");
        }
    }
}

<?php

namespace App\Domains\Inventory\Services;

use App\Domains\Inventory\Models\InventoryBatch;
use App\Domains\Inventory\Models\StockMovement;
use Exception;

class ValuationEngine
{
    /**
     * Deduct stock based on Valuation Method (FIFO or FEFO)
     */
    public function deductStock(int $productId, int $quantityToDeduct, string $method = 'FIFO')
    {
        $query = InventoryBatch::where('product_id', $productId)
                    ->where('available_quantity', '>', 0);

        if ($method === 'FEFO') {
            // First Expiring, First Out
            $query->orderBy('expiry_date', 'asc')->orderBy('created_at', 'asc');
        } else {
            // First In, First Out (FIFO)
            $query->orderBy('created_at', 'asc');
        }

        $batches = $query->get();
        $remainingToDeduct = $quantityToDeduct;
        $totalCostOfGoodsSold = 0;
        $deductedBatches = [];

        foreach ($batches as $batch) {
            if ($remainingToDeduct <= 0) break;

            $deductFromThisBatch = min($batch->available_quantity, $remainingToDeduct);
            
            // Deduct
            $batch->available_quantity -= $deductFromThisBatch;
            $batch->save();

            // Record Movement
            StockMovement::create([
                'inventory_batch_id' => $batch->id,
                'type' => 'out',
                'quantity' => $deductFromThisBatch,
                'reference_type' => 'OrderDeduction',
            ]);

            // Calculate COGS
            $totalCostOfGoodsSold += ($deductFromThisBatch * $batch->unit_cost);
            
            $deductedBatches[] = [
                'batch_id' => $batch->id,
                'quantity' => $deductFromThisBatch,
                'cost' => $batch->unit_cost
            ];

            $remainingToDeduct -= $deductFromThisBatch;
        }

        if ($remainingToDeduct > 0) {
            throw new Exception("Insufficient stock to fulfill order for product ID: {$productId}");
        }

        return [
            'total_cogs' => $totalCostOfGoodsSold,
            'deducted_batches' => $deductedBatches
        ];
    }
}

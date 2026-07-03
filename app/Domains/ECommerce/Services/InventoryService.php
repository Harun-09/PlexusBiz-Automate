<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\InventoryMovementType;
use App\Domains\ECommerce\Models\InventoryMovement;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function assertAvailable(Product $product, int $quantity): void
    {
        if ($quantity > $product->availableStock()) {
            throw ValidationException::withMessages([
                'stock' => sprintf('%s has only %d available units.', $product->name, $product->availableStock()),
            ]);
        }
    }

    public function stockIn(Product $product, int $quantity, ?User $actor = null, ?string $reason = null): InventoryMovement
    {
        return $this->move($product, InventoryMovementType::StockIn, $quantity, $actor, $reason);
    }

    public function deductForOrder(Product $product, int $quantity, Model $order, ?User $actor = null): InventoryMovement
    {
        $this->assertAvailable($product, $quantity);

        return $this->move(
            product: $product,
            type: InventoryMovementType::StockOut,
            quantity: $quantity,
            actor: $actor,
            reason: 'Order checkout',
            reference: $order,
        );
    }

    private function move(
        Product $product,
        InventoryMovementType $type,
        int $quantity,
        ?User $actor = null,
        ?string $reason = null,
        ?Model $reference = null,
        array $metadata = [],
    ): InventoryMovement {
        $before = (int) ($product->stock_quantity ?? 0);
        $after = match ($type) {
            InventoryMovementType::StockIn, InventoryMovementType::OrderRelease => $before + $quantity,
            InventoryMovementType::StockOut, InventoryMovementType::OrderReserve => $before - $quantity,
            InventoryMovementType::Adjustment => $quantity,
        };

        if ($after < 0) {
            throw ValidationException::withMessages([
                'stock' => sprintf('Inventory for %s cannot go below zero.', $product->name),
            ]);
        }

        $product->forceFill(['stock_quantity' => $after])->save();

        if ($type === InventoryMovementType::StockOut) {
            $threshold = config('commerce.low_stock_threshold', 20);
            if ($after <= $threshold) {
                $supplierUser = User::where('id', $product->supplier?->user_id ?? 0)->first() 
                             ?? User::role('admin')->first(); // Fallback to admin if supplier user not found

                if ($supplierUser) {
                    $supplierUser->notify(new \App\Notifications\LowStockAlert($product, $after));
                }
            }
        }

        return InventoryMovement::create([
            'product_id' => $product->id,
            'supplier_id' => $product->supplier_id,
            'created_by' => $actor?->id,
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
            'reason' => $reason,
            'metadata' => $metadata,
        ]);
    }
}

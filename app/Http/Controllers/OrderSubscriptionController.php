<?php

namespace App\Http\Controllers;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Services\CartService;
use App\Domains\ECommerce\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderSubscriptionController extends Controller
{
    protected CartService $cartService;
    protected InventoryService $inventory;

    public function __construct(CartService $cartService, InventoryService $inventory)
    {
        $this->cartService = $cartService;
        $this->inventory = $inventory;
    }

    public function repeat(Request $request, Order $order)
    {
        $user = $request->user();

        // Check if order belongs to user
        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        $itemsAdded = 0;
        $unavailableMessages = [];

        DB::transaction(function () use ($order, $user, &$itemsAdded, &$unavailableMessages) {
            foreach ($order->items as $item) {
                $product = $item->product;
                
                if (!$product || $product->status->value !== 'active') {
                    $unavailableMessages[] = "{$item->product_name} is no longer available.";
                    continue;
                }

                $availableStock = $this->inventory->availableQuantity($product);
                $desiredQuantity = $item->quantity;

                if ($availableStock <= 0) {
                    $unavailableMessages[] = "{$product->name} is out of stock.";
                    continue;
                }

                $quantityToAdd = min($desiredQuantity, $availableStock);

                if ($quantityToAdd < $desiredQuantity) {
                    $unavailableMessages[] = "Only {$quantityToAdd} units of {$product->name} are available and were added to cart.";
                }

                $this->cartService->addItem($user, $product, $quantityToAdd);
                $itemsAdded++;
            }
        });

        if ($itemsAdded === 0) {
            return redirect()->back()->with('error', 'None of the items in this order are currently available.');
        }

        $successMessage = "Items successfully added to your cart.";
        if (!empty($unavailableMessages)) {
            $successMessage .= " Note: " . implode(' ', $unavailableMessages);
        }

        return redirect()->route('cart.index')->with('success', $successMessage);
    }

    public function toggle(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        $order->subscription_active = !$order->subscription_active;
        $order->is_subscription = true;
        $order->save();

        $status = $order->subscription_active ? 'activated' : 'deactivated';
        
        return redirect()->back()->with('success', "Monthly subscription for this order has been {$status}.");
    }
}

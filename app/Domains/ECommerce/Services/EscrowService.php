<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Enums\EscrowStatus;
use Illuminate\Support\Facades\DB;
use Exception;

class EscrowService
{
    protected CommissionCalculatorService $commissionService;

    public function __construct(CommissionCalculatorService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Release funds from Escrow to the Supplier's wallet, deducting platform commission.
     * This is called when a Buyer confirms they have received the order.
     */
    public function release(Order $order): bool
    {
        if ($order->escrow_status !== EscrowStatus::Held) {
            throw new Exception("Escrow is not held for this order.");
        }

        if (! $order->isPaid()) {
            throw new Exception("Cannot release escrow for unpaid order.");
        }

        // Calculate commission
        $commissionAmount = $this->commissionService->calculate($order->grand_total);
        $payableToSupplier = $order->grand_total - $commissionAmount;

        DB::beginTransaction();

        try {
            // Update Order
            $order->forceFill([
                'escrow_status' => EscrowStatus::Released->value,
                'commission_amount' => $commissionAmount,
            ])->save();

            // Find suppliers for this order via order items or supplier orders
            // In this multi-vendor system, an Order usually belongs to one SupplierOrder.
            $supplierOrders = $order->supplierOrders()->with('supplier')->get();

            foreach ($supplierOrders as $supplierOrder) {
                $supplier = $supplierOrder->supplier;
                if ($supplier) {
                    // For simplicity, giving total payable to the first supplier if there's only one.
                    // If multiple, would need to calculate per-supplier-order. Let's assume per-supplier-order logic:
                    $supplierCommission = $this->commissionService->calculate($supplierOrder->total);
                    $supplierPayable = $supplierOrder->total - $supplierCommission;
                    
                    $supplier->increment('wallet_balance', $supplierPayable);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

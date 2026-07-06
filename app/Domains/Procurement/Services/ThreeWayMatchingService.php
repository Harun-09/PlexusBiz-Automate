<?php

namespace App\Domains\Procurement\Services;

use App\Domains\Procurement\Models\PurchaseOrder;
use App\Domains\Procurement\Models\GoodsReceiptNote;
use App\Domains\Procurement\Models\SupplierInvoice;
use Exception;

class ThreeWayMatchingService
{
    /**
     * Perform Three-Way Matching on PO, GRN, and Invoice.
     * Ensure invoiced quantities and amounts align with what was ordered and received.
     */
    public function match(PurchaseOrder $po, GoodsReceiptNote $grn, SupplierInvoice $invoice, float $tolerancePercent = 2.0)
    {
        // 1. Verify links
        if ($grn->purchase_order_id !== $po->id || $invoice->purchase_order_id !== $po->id) {
            throw new Exception("Documents do not belong to the same Purchase Order.");
        }

        // 2. Quantity Match (GRN vs PO vs Invoice)
        $orderedQuantities = $po->items->pluck('quantity', 'product_id')->toArray();
        // Since we stored JSON of {"po_item_id": quantity}, let's map it. For MVP we assume full GRN.
        $receivedQuantities = $grn->received_quantities ?? [];
        $invoicedQuantities = $invoice->invoiced_quantities ?? [];

        if (empty($receivedQuantities) && $grn->status === 'received') {
            // Assume 100% received if not specified
            $receivedQuantities = $po->items->pluck('quantity', 'id')->toArray();
        }

        if (empty($invoicedQuantities)) {
            $invoicedQuantities = $po->items->pluck('quantity', 'id')->toArray();
        }

        foreach ($po->items as $item) {
            $received = $receivedQuantities[$item->id] ?? 0;
            $invoiced = $invoicedQuantities[$item->id] ?? 0;

            if ($invoiced > $received) {
                throw new Exception("Invoiced quantity for item {$item->id} exceeds received quantity.");
            }
            if ($invoiced > $item->quantity) {
                throw new Exception("Invoiced quantity for item {$item->id} exceeds ordered quantity.");
            }
        }

        // 3. Price/Amount Match (Invoice Total vs PO Total)
        $poTotal = $po->total_amount;
        $invoiceTotal = $invoice->total_amount;

        $variance = abs($poTotal - $invoiceTotal);
        $variancePercent = ($variance / max($poTotal, 1)) * 100;

        if ($variancePercent > $tolerancePercent) {
            throw new Exception("Invoice amount variance ({$variancePercent}%) exceeds tolerance ({$tolerancePercent}%).");
        }

        // Match successful
        $invoice->update(['status' => 'matched']);

        // Dispatch event for Tax/Finance domain to record Purchase (Mushak 6.1) and AP Ledger
        // In a real app we'd dispatch(new \App\Domains\Procurement\Events\PurchaseRecorded(...))
        // But since we created it in Tax domain for Phase 3.1:
        event(new \App\Domains\Tax\Events\PurchaseRecorded([
            'purchase_id' => $po->id,
            'amount' => $invoiceTotal,
            // Mock VAT amount for now (e.g., 15% standard rate)
            'vat_amount' => round($invoiceTotal * 0.15, 2), 
            'vds_amount' => 0.0,
        ]));

        return true;
    }
}

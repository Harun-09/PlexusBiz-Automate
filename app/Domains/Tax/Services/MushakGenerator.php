<?php

namespace App\Domains\Tax\Services;

use App\Domains\ECommerce\Models\Order;
use App\Domains\Tax\Models\TaxInvoice;
use App\Domains\Tax\Models\MushakDocument;
use Illuminate\Support\Str;

class MushakGenerator
{
    protected VatCalculator $vatCalculator;

    public function __construct(VatCalculator $vatCalculator)
    {
        $this->vatCalculator = $vatCalculator;
    }

    /**
     * Generate Mushak 6.3 (Tax Invoice) for an Order
     */
    public function generateMushak63(Order $order): TaxInvoice
    {
        $totalVat = $this->vatCalculator->calculateTotalVatForOrder($order);
        
        $taxInvoiceNumber = 'M63-' . date('Ymd') . '-' . Str::upper(Str::random(6));

        // Create the Tax Invoice record (using existing table)
        $taxInvoice = TaxInvoice::create([
            'order_id' => $order->id,
            'tax_invoice_number' => $taxInvoiceNumber,
            // In a real app, generate PDF here and save path
            'file_path' => 'mushaks/6_3/' . $taxInvoiceNumber . '.pdf', 
        ]);

        // Create the specific Mushak 6.3 Document metadata
        MushakDocument::create([
            'tax_invoice_id' => $taxInvoice->id,
            'form_type' => '6.3',
            'issue_date' => now(),
            'challan_number' => $taxInvoiceNumber,
            'total_vat_amount' => $totalVat,
            'pdf_path' => $taxInvoice->file_path,
        ]);

        return $taxInvoice;
    }
}

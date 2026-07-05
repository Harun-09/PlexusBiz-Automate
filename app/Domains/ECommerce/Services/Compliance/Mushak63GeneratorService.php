<?php

namespace App\Domains\ECommerce\Services\Compliance;

use App\Domains\ECommerce\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Mushak63GeneratorService
{
    /**
     * Generate NBR Mushak 6.3 PDF
     */
    public function generate(Order $order): string
    {
        $taxInvoiceNumber = 'M63-' . strtoupper(Str::random(8));

        // Note: Real implementation would load a specific blade view for Mushak 6.3 format
        // and calculate individual product VAT amounts properly.
        $data = [
            'order' => $order,
            'tax_invoice_number' => $taxInvoiceNumber,
            'vat_reg_no' => config('commerce.company_bin', '000000000-0000'),
            'issue_date' => now()->format('Y-m-d H:i:s'),
        ];

        // Mock View logic for PDF
        // $pdf = Pdf::loadView('pdf.mushak_6_3', $data);
        $pdf = Pdf::loadHTML('<h1>Mushak 6.3 - Tax Invoice</h1><p>Invoice No: ' . $taxInvoiceNumber . '</p><p>Total VAT: ' . $order->tax_total . '</p>');

        $path = 'mushaks/' . $taxInvoiceNumber . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return Storage::disk('public')->url($path);
    }
}

<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class InvoicePdfService
{
    public function generateForOrder(Order $order): Invoice
    {
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'status' => InvoiceStatus::Issued,
            'subtotal' => $order->subtotal,
            'tax_total' => $order->tax_total,
            'total' => $order->grand_total,
            'issued_at' => now(),
            'due_at' => now()->addDays(30),
        ]);

        $this->generatePdf($invoice);

        return $invoice;
    }

    public function generatePdf(Invoice $invoice): string
    {
        $pdf = Pdf::loadView('invoices.template', [
            'invoice' => $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']),
        ]);

        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';
        $path = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $filename;
    }

    public function download(Invoice $invoice): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = Pdf::loadView('invoices.template', [
            'invoice' => $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']),
        ]);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function stream(Invoice $invoice): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = Pdf::loadView('invoices.template', [
            'invoice' => $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']),
        ]);

        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
    }
}

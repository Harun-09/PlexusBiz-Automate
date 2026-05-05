<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function download(Invoice $invoice): StreamedResponse
    {
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        $pdfContents = $this->renderPdfContents($invoice);

        return response()->streamDownload(function () use ($pdfContents): void {
            echo $pdfContents;
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => (string) strlen($pdfContents),
        ]);
    }

    public function stream(Invoice $invoice): StreamedResponse
    {
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        $pdfContents = $this->renderPdfContents($invoice);

        return response()->stream(function () use ($pdfContents): void {
            echo $pdfContents;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => (string) strlen($pdfContents),
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
    }

    private function renderPdfContents(Invoice $invoice): string
    {
        $pdf = Pdf::loadView('invoices.template', [
            'invoice' => $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']),
        ]);

        return $pdf->output();
    }
}

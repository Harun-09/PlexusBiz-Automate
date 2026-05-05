<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
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
            'invoice' => $this->loadInvoice($invoice),
        ]);

        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';
        $path = storage_path('app/public/' . $filename);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $filename;
    }

    public function download(Invoice $invoice): Response
    {
        return Pdf::loadView('invoices.template', [
            'invoice' => $this->loadInvoice($invoice),
        ])->download($this->downloadName($invoice));
    }

    public function stream(Invoice $invoice): Response
    {
        return Pdf::loadView('invoices.template', [
            'invoice' => $this->loadInvoice($invoice),
        ])->stream($this->downloadName($invoice));
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
    }

    private function downloadName(Invoice $invoice): string
    {
        return 'invoice-' . $invoice->invoice_number . '.pdf';
    }

    private function loadInvoice(Invoice $invoice): Invoice
    {
        return $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']);
    }
}

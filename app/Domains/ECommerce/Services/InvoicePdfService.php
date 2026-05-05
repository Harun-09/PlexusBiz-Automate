<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Enums\InvoiceStatus;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

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

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $filename;
    }

    public function download(Invoice $invoice): Response
    {
        [$invoice, $pdfContents, $downloadName] = $this->invoicePdfPayload($invoice);

        return $this->renderDownloadPage($invoice, $pdfContents, $downloadName);
    }

    public function stream(Invoice $invoice): Response
    {
        [$invoice, $pdfContents, $downloadName] = $this->invoicePdfPayload($invoice);

        return $this->renderPreviewPage($invoice, $pdfContents, $downloadName);
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
    }

    /**
     * @return array{0: Invoice, 1: string, 2: string}
     */
    private function invoicePdfPayload(Invoice $invoice): array
    {
        $invoice = $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']);

        return [
            $invoice,
            $this->renderPdfContents($invoice),
            'invoice-' . $invoice->invoice_number . '.pdf',
        ];
    }

    private function renderPdfContents(Invoice $invoice): string
    {
        $pdf = Pdf::loadView('invoices.template', [
            'invoice' => $invoice->load(['order.buyer', 'order.items.product', 'order.items.supplier']),
        ]);

        return $pdf->output();
    }

    private function renderPreviewPage(Invoice $invoice, string $pdfContents, string $downloadName): Response
    {
        $payloadJson = $this->browserPayload($pdfContents, $downloadName);
        $invoiceNumber = htmlspecialchars($invoice->invoice_number, ENT_QUOTES, 'UTF-8');
        $orderNumber = htmlspecialchars($invoice->order?->order_number ?? '-', ENT_QUOTES, 'UTF-8');
        $total = htmlspecialchars(number_format((float) ($invoice->total ?? 0), 2, '.', ''), ENT_QUOTES, 'UTF-8');
        $currency = htmlspecialchars((string) ($invoice->order?->currency ?? 'BDT'), ENT_QUOTES, 'UTF-8');
        $showUrl = htmlspecialchars(route('invoices.show', $invoice), ENT_QUOTES, 'UTF-8');
        $downloadUrl = htmlspecialchars(route('invoices.download', $invoice), ENT_QUOTES, 'UTF-8');

        return response(<<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview invoice {$invoiceNumber}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7fb;
            color: #0f172a;
        }
        .shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            padding: 1rem 1.25rem;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }
        .title {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }
        .meta {
            margin: 0.35rem 0 0;
            font-size: 0.875rem;
            color: #64748b;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.5rem;
            padding: 0.55rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid transparent;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-secondary {
            border-color: #cbd5e1;
            background: #fff;
            color: #334155;
        }
        .btn-primary {
            background: #0f172a;
            color: #fff;
        }
        .notice {
            margin: 0.75rem 1rem 0;
            padding: 0.75rem 1rem;
            border: 1px solid #dbeafe;
            border-radius: 0.75rem;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .viewer-wrap {
            flex: 1;
            min-height: 0;
            padding: 1rem;
        }
        .frame-card {
            height: 100%;
            min-height: calc(100vh - 7rem);
            border: 1px solid #dbe2ee;
            border-radius: 1rem;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        .viewer {
            width: 100%;
            height: 100%;
            min-height: calc(100vh - 7rem);
            border: 0;
            background: #fff;
        }
        .fallback {
            display: none;
            padding: 1rem 1.25rem;
            font-size: 0.875rem;
            color: #475569;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="toolbar">
            <div>
                <p class="title">Invoice preview</p>
                <p class="meta">Invoice {$invoiceNumber} · Order {$orderNumber} · {$currency} {$total}</p>
            </div>
            <div class="actions">
                <a class="btn btn-secondary" href="{$showUrl}">Open invoice</a>
                <a class="btn btn-primary" href="{$downloadUrl}">Download PDF</a>
            </div>
        </header>
        <div class="notice">The preview uses a browser-safe PDF blob so it is not blocked by a direct PDF navigation.</div>
        <div class="viewer-wrap">
            <div class="frame-card">
                <iframe id="viewer" class="viewer" title="Invoice preview"></iframe>
                <div id="fallback" class="fallback">If the embedded preview does not render, use the download button above.</div>
            </div>
        </div>
    </div>
    <script>
    (function () {
        var payload = {$payloadJson};
        var binary = atob(payload.base64);
        var length = binary.length;
        var bytes = new Uint8Array(length);

        for (var index = 0; index < length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        var blob = new Blob([bytes], { type: payload.mimeType });
        var blobUrl = URL.createObjectURL(blob);
        var viewer = document.getElementById('viewer');
        var fallback = document.getElementById('fallback');

        viewer.src = blobUrl;

        window.setTimeout(function () {
            fallback.style.display = 'block';
        }, 2000);

        window.addEventListener('beforeunload', function () {
            URL.revokeObjectURL(blobUrl);
        });
    }());
    </script>
</body>
</html>
HTML, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function renderDownloadPage(Invoice $invoice, string $pdfContents, string $downloadName): Response
    {
        $payloadJson = $this->browserPayload($pdfContents, $downloadName);
        $invoiceNumber = htmlspecialchars($invoice->invoice_number, ENT_QUOTES, 'UTF-8');
        $showUrl = htmlspecialchars(route('invoices.show', $invoice), ENT_QUOTES, 'UTF-8');
        $fallbackName = htmlspecialchars($downloadName, ENT_QUOTES, 'UTF-8');

        return response(<<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Downloading invoice {$invoiceNumber}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            color: #0f172a;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
        }
        .card {
            width: min(560px, 100%);
            background: #fff;
            border: 1px solid #dbe2ee;
            border-radius: 1.25rem;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
            padding: 1.5rem;
        }
        h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
        }
        p {
            margin: 0.75rem 0 0;
            line-height: 1.6;
            color: #475569;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.75rem;
            padding: 0.65rem 1rem;
            border-radius: 0.9rem;
            border: 1px solid transparent;
            font-size: 0.9375rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-secondary {
            border-color: #cbd5e1;
            background: #fff;
            color: #334155;
        }
        .btn-primary {
            background: #0f172a;
            color: #fff;
        }
        .hint {
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        .status {
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.9rem;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Preparing invoice download</h1>
        <p>Invoice {$invoiceNumber} is ready. The download starts automatically, and the manual button stays available if your browser blocks the first attempt.</p>
        <div class="actions">
            <a class="btn btn-secondary" href="{$showUrl}">Open invoice</a>
            <a id="manual-download" class="btn btn-primary" href="#" download="{$fallbackName}">Start download</a>
        </div>
        <div id="status" class="status">Downloading...</div>
        <div class="hint">If nothing happens, click <strong>Start download</strong>.</div>
    </div>
    <script>
    (function () {
        var payload = {$payloadJson};
        var binary = atob(payload.base64);
        var length = binary.length;
        var bytes = new Uint8Array(length);

        for (var index = 0; index < length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        var blob = new Blob([bytes], { type: payload.mimeType });
        var blobUrl = URL.createObjectURL(blob);
        var link = document.getElementById('manual-download');
        var status = document.getElementById('status');

        link.href = blobUrl;
        link.download = payload.fileName;

        window.setTimeout(function () {
            link.click();
            status.textContent = 'Download started. If your browser blocked it, use the button above.';
        }, 150);

        window.addEventListener('beforeunload', function () {
            URL.revokeObjectURL(blobUrl);
        });
    }());
    </script>
</body>
</html>
HTML, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function browserPayload(string $pdfContents, string $downloadName): string
    {
        $payload = [
            'fileName' => $downloadName,
            'mimeType' => 'application/pdf',
            'base64' => base64_encode($pdfContents),
        ];

        return json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
            ?: '{"fileName":"","mimeType":"application/pdf","base64":""}';
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html,
        body {
            width: 100%;
        }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.34;
            color: #334155;
            background: #ffffff;
        }

        .page {
            width: 100%;
        }

        .header {
            width: 100%;
            margin-bottom: 9px;
            padding: 12px 14px;
            border: 1px solid #dbeafe;
            border-bottom: 2px solid #2563eb;
            border-radius: 12px;
            background: #f8fbff;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand {
            width: 58%;
            padding-right: 12px;
        }

        .brand h1 {
            margin-bottom: 3px;
            color: #2563eb;
            font-size: 22px;
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .brand p {
            margin-bottom: 1px;
            color: #64748b;
            font-size: 9px;
        }

        .invoice-meta {
            width: 42%;
            text-align: right;
        }

        .invoice-meta h2 {
            margin-bottom: 5px;
            color: #2563eb;
            font-size: 18px;
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .meta-line {
            margin-bottom: 1px;
            color: #475569;
            font-size: 9px;
        }

        .status-badge {
            display: inline-block;
            margin-top: 3px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .status-issued {
            background-color: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .status-paid {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .section-title {
            margin-bottom: 5px;
            color: #2563eb;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .info-table td {
            width: 50%;
            vertical-align: top;
        }

        .info-card {
            min-height: 72px;
            padding: 8px 10px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #ffffff;
        }

        .info-card p {
            margin-bottom: 2px;
        }

        .label {
            color: #64748b;
            font-weight: 700;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .items-table thead th {
            padding: 7px 8px;
            background: #2563eb;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-align: left;
        }

        .items-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
            vertical-align: top;
            word-break: break-word;
        }

        .items-table tr:nth-child(even) {
            background: #f8fbff;
        }

        .items-table tr {
            page-break-inside: avoid;
        }

        .item-name {
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }

        .item-sku {
            margin-top: 1px;
            color: #64748b;
            font-size: 8.5px;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            width: 280px;
            margin-top: 10px;
            margin-left: auto;
            page-break-inside: avoid;
        }

        .summary-box {
            padding: 8px 10px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #f8fbff;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 4px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-table tr.total td {
            padding-top: 6px;
            border-top: 2px solid #2563eb;
            border-bottom: none;
            color: #2563eb;
            font-size: 11.5px;
            font-weight: 800;
        }

        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #dbeafe;
            color: #64748b;
            font-size: 9px;
            line-height: 1.3;
            text-align: center;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="brand">
                        <h1>PlexusBiz Automate</h1>
                        <p>B2B E-Commerce & Business Automation Platform</p>
                        <p>Email: support@plexusbiz.com</p>
                    </td>
                    <td class="invoice-meta">
                        <h2>INVOICE</h2>
                        <p class="meta-line"><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                        <p class="meta-line"><strong>Order Number:</strong> {{ $invoice->order->order_number }}</p>
                        <p class="meta-line"><strong>Date:</strong> {{ $invoice->issued_at->format('M d, Y') }}</p>
                        <p class="meta-line"><strong>Due Date:</strong> {{ $invoice->due_at->format('M d, Y') }}</p>
                        <p>
                            <span class="status-badge status-{{ $invoice->status->value }}">
                                {{ ucfirst($invoice->status->value) }}
                            </span>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table class="info-table">
                <tr>
                    <td>
                        <div class="info-card">
                            <div class="section-title">Bill To</div>
                            <p><strong>{{ $invoice->order->buyer->name }}</strong></p>
                            <p>Email: {{ $invoice->order->buyer->email }}</p>
                            <p>Customer ID: CUST-{{ $invoice->order->buyer->id }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="info-card">
                            <div class="section-title">Order Details</div>
                            <p><span class="label">Order Date:</span> {{ $invoice->order->placed_at?->format('M d, Y') ?? $invoice->order->created_at->format('M d, Y') }}</p>
                            <p><span class="label">Order Status:</span> {{ ucfirst($invoice->order->status->value) }}</p>
                            <p><span class="label">Currency:</span> {{ $invoice->order->currency }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Order Items</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Product</th>
                        <th style="width: 20%;">Supplier</th>
                        <th style="width: 10%;">Qty</th>
                        <th class="text-right" style="width: 15%;">Unit Price</th>
                        <th class="text-right" style="width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->order->items as $item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $item->product_name }}</div>
                                <div class="item-sku">SKU: {{ $item->sku }}</div>
                            </td>
                            <td>{{ $item->supplier->company_name ?? $item->supplier->user->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }} {{ $invoice->order->currency }}</td>
                            <td class="text-right">{{ number_format($item->total, 2) }} {{ $invoice->order->currency }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary">
            <div class="summary-box">
                <table class="summary-table">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->order->currency }}</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td class="text-right">{{ number_format($invoice->tax_total, 2) }} {{ $invoice->order->currency }}</td>
                    </tr>
                    @if($invoice->order->discount_total > 0)
                        <tr>
                            <td>Discount</td>
                            <td class="text-right">-{{ number_format($invoice->order->discount_total, 2) }} {{ $invoice->order->currency }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td>Total Amount</td>
                        <td class="text-right">{{ number_format($invoice->total, 2) }} {{ $invoice->order->currency }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your business. This invoice was generated automatically by PlexusBiz Automate.</p>
            <p>For any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>

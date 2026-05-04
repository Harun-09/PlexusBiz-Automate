<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        .company-info h1 {
            color: #2563eb;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #666;
            font-size: 12px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta h2 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .invoice-meta p {
            font-size: 12px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
        }
        .info-box {
            width: 48%;
        }
        .info-box p {
            margin-bottom: 5px;
        }
        .info-box .label {
            font-weight: bold;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            margin-top: 30px;
            margin-left: auto;
            width: 300px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            border-bottom: none;
        }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-issued {
            background-color: #dbeafe;
            color: #2563eb;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #059669;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1>PlexusBiz Automate</h1>
            <p>B2B E-Commerce & Business Automation Platform</p>
            <p>Email: support@plexusbiz.com</p>
        </div>
        <div class="invoice-meta">
            <h2>INVOICE</h2>
            <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Order Number:</strong> {{ $invoice->order->order_number }}</p>
            <p><strong>Date:</strong> {{ $invoice->issued_at->format('M d, Y') }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_at->format('M d, Y') }}</p>
            <p>
                <span class="status-badge status-{{ $invoice->status->value }}">
                    {{ ucfirst($invoice->status->value) }}
                </span>
            </p>
        </div>
    </div>

    <div class="section">
        <div class="info-grid">
            <div class="info-box">
                <div class="section-title">Bill To</div>
                <p><strong>{{ $invoice->order->buyer->name }}</strong></p>
                <p>Email: {{ $invoice->order->buyer->email }}</p>
                <p>Customer ID: CUST-{{ $invoice->order->buyer->id }}</p>
            </div>
            <div class="info-box">
                <div class="section-title">Order Details</div>
                <p><span class="label">Order Date:</span> {{ $invoice->order->placed_at?->format('M d, Y') ?? $invoice->order->created_at->format('M d, Y') }}</p>
                <p><span class="label">Order Status:</span> {{ ucfirst($invoice->order->status->value) }}</p>
                <p><span class="label">Currency:</span> {{ $invoice->order->currency }}</p>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Order Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Product</th>
                    <th style="width: 20%;">Supplier</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;" class="text-right">Unit Price</th>
                    <th style="width: 15%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong><br>
                        <small>SKU: {{ $item->sku }}</small>
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
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>{{ number_format($invoice->subtotal, 2) }} {{ $invoice->order->currency }}</span>
        </div>
        <div class="summary-row">
            <span>Tax:</span>
            <span>{{ number_format($invoice->tax_total, 2) }} {{ $invoice->order->currency }}</span>
        </div>
        @if($invoice->order->discount_total > 0)
        <div class="summary-row">
            <span>Discount:</span>
            <span>-{{ number_format($invoice->order->discount_total, 2) }} {{ $invoice->order->currency }}</span>
        </div>
        @endif
        <div class="summary-row total">
            <span>Total Amount:</span>
            <span>{{ number_format($invoice->total, 2) }} {{ $invoice->order->currency }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This invoice was generated automatically by PlexusBiz Automate.</p>
        <p>For any questions, please contact our support team.</p>
    </div>
</body>
</html>

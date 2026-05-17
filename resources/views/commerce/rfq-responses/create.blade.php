<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit RFQ Quote</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 24px; color: #111827;">
    <h1 style="margin: 0 0 8px;">Submit RFQ Quote</h1>
    <p style="margin: 0 0 20px; color: #4b5563;">
        RFQ #{{ $rfq['rfq_number'] ?? $rfq['id'] }} for {{ $supplier['company_name'] ?? 'Supplier' }}
    </p>

    @if ($errors->any())
        <div style="border: 1px solid #ef4444; background: #fef2f2; padding: 12px; margin-bottom: 16px;">
            <strong style="display: block; margin-bottom: 8px;">Please fix the following:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('commerce.rfq-responses.store', $rfq['id']) }}" style="max-width: 680px;">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
            <label style="display: block;">
                <span style="display: block; margin-bottom: 6px;">Quoted Amount *</span>
                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    name="quoted_amount"
                    value="{{ old('quoted_amount', $response['quoted_amount'] ?? '') }}"
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db;"
                >
            </label>

            <label style="display: block;">
                <span style="display: block; margin-bottom: 6px;">Currency</span>
                <input
                    type="text"
                    name="currency"
                    maxlength="3"
                    value="{{ old('currency', $response['currency'] ?? 'BDT') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db;"
                >
            </label>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
            <label style="display: block;">
                <span style="display: block; margin-bottom: 6px;">Min Order Quantity</span>
                <input
                    type="number"
                    name="min_order_quantity"
                    min="1"
                    value="{{ old('min_order_quantity', $response['min_order_quantity'] ?? '') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db;"
                >
            </label>

            <label style="display: block;">
                <span style="display: block; margin-bottom: 6px;">Lead Time (Days)</span>
                <input
                    type="number"
                    name="lead_time_days"
                    min="0"
                    value="{{ old('lead_time_days', $response['lead_time_days'] ?? '') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db;"
                >
            </label>
        </div>

        <label style="display: block; margin-bottom: 12px;">
            <span style="display: block; margin-bottom: 6px;">Valid Until</span>
            <input
                type="date"
                name="valid_until"
                value="{{ old('valid_until', $response['valid_until'] ?? '') }}"
                style="width: 100%; padding: 10px; border: 1px solid #d1d5db;"
            >
        </label>

        <label style="display: block; margin-bottom: 12px;">
            <span style="display: block; margin-bottom: 6px;">Message</span>
            <textarea
                name="message"
                rows="4"
                style="width: 100%; padding: 10px; border: 1px solid #d1d5db;"
            >{{ old('message', $response['message'] ?? '') }}</textarea>
        </label>

        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="submit" style="padding: 10px 16px; border: 0; background: #111827; color: #ffffff; cursor: pointer;">
                Submit Quote
            </button>
            <a href="{{ route('commerce.rfq-responses.index') }}" style="color: #2563eb; text-decoration: none;">
                Back to RFQ Responses
            </a>
        </div>
    </form>
</body>
</html>

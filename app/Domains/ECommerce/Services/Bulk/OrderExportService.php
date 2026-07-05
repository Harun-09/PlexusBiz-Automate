<?php

namespace App\Domains\ECommerce\Services\Bulk;

use App\Domains\ECommerce\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderExportService
{
    /**
     * Export orders to CSV
     */
    public function export(array $filters = []): string
    {
        $query = Order::with('items');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('placed_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('placed_at', '<=', $filters['date_to']);
        }

        $orders = $query->get();
        $filename = 'exports/orders_' . date('Ymd_His') . '_' . Str::random(5) . '.csv';
        
        Storage::disk('local')->makeDirectory('exports');
        $filePath = Storage::disk('local')->path($filename);
        
        $file = fopen($filePath, 'w');
        
        // Header
        fputcsv($file, ['Order ID', 'Order Number', 'Status', 'Payment Term', 'Subtotal', 'Grand Total', 'Currency', 'Placed At']);
        
        foreach ($orders as $order) {
            fputcsv($file, [
                $order->id,
                $order->order_number,
                $order->status,
                $order->payment_term,
                $order->subtotal,
                $order->grand_total,
                $order->currency,
                $order->placed_at,
            ]);
        }

        fclose($file);

        return $filePath;
    }
}

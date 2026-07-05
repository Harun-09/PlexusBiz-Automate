<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Services\Logistics\LogisticsBrokerService;

class BulkFulfillOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:bulk-fulfill {--limit=50 : The maximum number of orders to fulfill}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch pending orders to the Logistics Broker Service';

    /**
     * Execute the console command.
     */
    public function handle(LogisticsBrokerService $broker)
    {
        $limit = $this->option('limit');
        
        $orders = Order::where('status', OrderStatus::PAID) // Assume paid orders are ready for fulfillment
            ->whereNull('consignment_id')
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No pending orders to fulfill.');
            return;
        }

        $this->info("Dispatching {$orders->count()} orders...");

        foreach ($orders as $order) {
            try {
                // Formatting payload for courier
                $orderData = [
                    'store_id' => $order->supplier_id,
                    'item_weight' => 1.5, // Default weight mock
                    'recipient_name' => $order->customer->name ?? 'Customer',
                    'recipient_phone' => $order->customer->phone ?? '01000000000',
                    'recipient_address' => 'Dhaka, Bangladesh',
                    'amount_to_collect' => $order->total_amount,
                ];

                $response = $broker->dispatchOrder($orderData);
                
                $order->update([
                    'status' => OrderStatus::SHIPPED, // Move to shipped state
                    'consignment_id' => $response['consignment_id'] ?? null,
                ]);

                $this->info("Order #{$order->id} dispatched via {$response['courier_used']}. Consignment: " . ($response['consignment_id'] ?? 'N/A'));

            } catch (\Exception $e) {
                $this->error("Failed to dispatch order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info('Bulk fulfillment completed.');
    }
}

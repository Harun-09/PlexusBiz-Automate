<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\ECommerce\Models\SupplierOrder;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\EscrowStatus;
use Illuminate\Support\Facades\Log;

class ProcessSupplierPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payouts:process {--limit=20 : The maximum number of payouts to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automated multi-vendor payouts via mocked bKash B2B API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        // Find supplier orders that are delivered, escrow is held, and are ready for payout (no disputes)
        $orders = SupplierOrder::where('status', OrderStatus::DELIVERED)
            ->where('escrow_status', EscrowStatus::Held)
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No pending payouts.');
            return;
        }

        $this->info("Processing {$orders->count()} supplier payouts...");

        foreach ($orders as $order) {
            try {
                // Mock bKash B2B Payout API Call
                // REAL API logic would use Http::post to bKash payout endpoint
                $isSuccess = true; // Mocked success
                
                if ($isSuccess) {
                    $order->update([
                        'escrow_status' => EscrowStatus::Released,
                    ]);
                    
                    Log::info("Payout successful for Supplier Order #{$order->id} (Amount: {$order->grand_total} {$order->currency})");
                    $this->info("Payout released for Order #{$order->id}");
                } else {
                    $this->error("Payout failed for Order #{$order->id}");
                }

            } catch (\Exception $e) {
                $this->error("Failed to process payout for Order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info('Payout processing completed.');
    }
}

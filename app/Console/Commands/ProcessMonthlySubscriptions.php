<?php

namespace App\Console\Commands;

use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Enums\OrderStatus;
use App\Domains\ECommerce\Enums\PaymentStatus;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

#[Signature('orders:process-subscriptions')]
#[Description('Process active monthly subscription orders and create new pending invoices.')]
class ProcessMonthlySubscriptions extends Command
{
    public function handle()
    {
        $this->info('Processing monthly subscriptions...');

        $today = now();

        $activeSubscriptions = Order::with('items', 'buyer')
            ->where('subscription_active', true)
            ->get()
            ->filter(function ($order) use ($today) {
                // Check if it's the same day of the month
                // (Very simple check for prototype. In production, we'd handle 29/30/31st edge cases and track last_billed_at)
                return $order->placed_at && $order->placed_at->day === $today->day && $order->placed_at->month !== $today->month;
            });

        $count = 0;

        foreach ($activeSubscriptions as $parentOrder) {
            DB::transaction(function () use ($parentOrder, &$count) {
                $newOrder = $parentOrder->replicate(['order_number', 'checkout_token', 'transaction_id']);
                $newOrder->order_number = 'SUB-' . strtoupper(Str::random(10));
                $newOrder->status = OrderStatus::Pending;
                $newOrder->payment_status = PaymentStatus::Pending;
                $newOrder->placed_at = now();
                // Subscriptions themselves don't automatically replicate the subscription flag to the child order unless we want a chain.
                // Let's keep the parent as the "subscription template" and the child as just a regular order, 
                // OR we can make the new one the subscription and deactivate the old one.
                // Let's just make the child a regular order.
                $newOrder->is_subscription = false;
                $newOrder->subscription_active = false;
                
                $newOrder->save();

                foreach ($parentOrder->items as $item) {
                    $newItem = $item->replicate(['order_id']);
                    $newItem->order_id = $newOrder->id;
                    $newItem->save();
                }

                // Send invoice notification to the buyer ($parentOrder->buyer)
                if ($parentOrder->buyer) {
                    $parentOrder->buyer->notify(new \App\Notifications\SubscriptionInvoiceGenerated($newOrder));
                }
                $count++;
            });
        }

        $this->info("Successfully processed {$count} subscriptions and generated new pending orders.");
    }
}

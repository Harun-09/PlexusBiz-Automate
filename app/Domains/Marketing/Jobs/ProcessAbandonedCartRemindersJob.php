<?php

namespace App\Domains\Marketing\Jobs;

use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\Marketing\Services\MarketingTriggerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAbandonedCartRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        CustomerProfileService $customers,
        MarketingTriggerService $marketing,
    ): void {
        Cart::query()
            ->with(['user', 'items'])
            ->where('status', CartStatus::Active->value)
            ->where('updated_at', '<=', now()->subDay())
            ->orderBy('id')
            ->each(function (Cart $cart) use ($customers, $marketing): void {
                if (! $cart->user) {
                    return;
                }

                if ($cart->items->isEmpty()) {
                    $cart->forceFill(['status' => CartStatus::Abandoned])->save();

                    return;
                }

                $customer = $customers->ensureForUser($cart->user, [
                    'contact_name' => $cart->user->name,
                    'email' => $cart->user->email,
                ]);

                $marketing->abandonedCartReminder($customer, [
                    'abandoned_cart_url' => route('cart.index'),
                    'cart_items_count' => $cart->items->sum('quantity'),
                    'cart_total' => number_format(
                        $cart->items->sum(fn ($item): float => (float) $item->unit_price * (int) $item->quantity),
                        2,
                        '.',
                        '',
                    ),
                ]);

                $cart->forceFill(['status' => CartStatus::Abandoned])->save();
            });
    }
}

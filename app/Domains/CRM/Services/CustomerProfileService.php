<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Models\Order;
use App\Models\User;

class CustomerProfileService
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function ensureForUser(User $user, array $attributes = []): Customer
    {
        $customer = Customer::firstOrNew(['user_id' => $user->id]);

        $profileFields = [
            'contact_name' => $attributes['contact_name'] ?? $user->name,
            'company_name' => $attributes['company_name'] ?? $user->company_name,
            'email' => $attributes['email'] ?? $user->email,
            'phone' => $attributes['phone'] ?? $user->phone,
            'business_type' => $attributes['business_type'] ?? $user->account_type,
        ];

        foreach ($profileFields as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if ($customer->exists && filled($customer->{$field}) && ! array_key_exists($field, $attributes)) {
                continue;
            }

            $customer->{$field} = $value;
        }

        if (array_key_exists('address', $attributes) && $attributes['address'] !== null) {
            $customer->address = $attributes['address'];
        }

        if (array_key_exists('tags', $attributes) && $attributes['tags'] !== null) {
            $customer->tags = $attributes['tags'];
        }

        $customer->status ??= CustomerStatus::Active;
        $customer->lifecycle_stage ??= CustomerLifecycleStage::Customer;
        $customer->last_activity_at = now();
        $customer->save();

        return $customer->refresh();
    }

    public function attachOrder(Customer $customer, Order $order): Order
    {
        $order->forceFill(['customer_id' => $customer->id])->save();

        $ordersCount = $customer->orders()->count();

        $customer->forceFill([
            'lifecycle_stage' => $ordersCount > 1 ? CustomerLifecycleStage::RepeatCustomer : CustomerLifecycleStage::Customer,
            'last_activity_at' => $order->placed_at ?? now(),
        ])->save();

        return $order->refresh();
    }

    /**
     * @return array{orders_count: int, total_spent: string, last_order_at: ?string}
     */
    public function purchaseSummary(Customer $customer): array
    {
        $orders = $customer->orders();

        return [
            'orders_count' => (int) $orders->count(),
            'total_spent' => number_format((float) $customer->orders()->sum('grand_total'), 2, '.', ''),
            'last_order_at' => optional($customer->orders()->latest('placed_at')->first()?->placed_at)->toIso8601String(),
        ];
    }
}

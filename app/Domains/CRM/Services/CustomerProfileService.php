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
        return Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'contact_name' => $attributes['contact_name'] ?? $user->name,
                'company_name' => $attributes['company_name'] ?? null,
                'email' => $attributes['email'] ?? $user->email,
                'phone' => $attributes['phone'] ?? null,
                'business_type' => $attributes['business_type'] ?? null,
                'address' => $attributes['address'] ?? null,
                'status' => CustomerStatus::Active,
                'lifecycle_stage' => CustomerLifecycleStage::Customer,
                'tags' => $attributes['tags'] ?? [],
                'last_activity_at' => now(),
            ],
        );
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

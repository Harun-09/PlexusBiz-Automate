<?php

namespace App\Policies;

use App\Domains\ECommerce\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'supplier', 'buyer']);
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('admin') || $order->buyer_id === $user->id) {
            return true;
        }

        $supplierId = $user->supplier?->id;

        return $supplierId !== null && $order->items()->where('supplier_id', $supplierId)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'buyer']);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole('admin') || $order->buyer_id === $user->id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('admin') || $order->buyer_id === $user->id;
    }
}

<?php

namespace App\Policies;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'supplier', 'buyer']);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasRole('admin')
            || $product->status === ProductStatus::Active
            || ($user->hasRole('supplier') && $product->supplier?->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('supplier') && $user->supplier?->isApproved());
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('supplier') && $product->supplier?->user_id === $user->id);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('supplier') && $product->supplier?->user_id === $user->id);
    }
}

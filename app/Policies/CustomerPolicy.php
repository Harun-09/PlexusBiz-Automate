<?php

namespace App\Policies;

use App\Domains\CRM\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']) || $customer->user_id === $user->id;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']) || $customer->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['admin', 'marketing_manager']);
    }
}

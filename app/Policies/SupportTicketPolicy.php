<?php

namespace App\Policies;

use App\Domains\Support\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'supplier', 'buyer']);
    }

    public function view(User $user, SupportTicket $supportTicket): bool
    {
        if ($user->hasRole('admin') || $supportTicket->requester_id === $user->id) {
            return true;
        }

        return $user->supplier?->id === $supportTicket->supplier_id;
    }
}

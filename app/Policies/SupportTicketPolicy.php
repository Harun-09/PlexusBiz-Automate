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
        return $this->canAccess($user, $supportTicket);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'supplier', 'buyer']);
    }

    public function reply(User $user, SupportTicket $supportTicket): bool
    {
        return $this->canAccess($user, $supportTicket);
    }

    public function update(User $user, SupportTicket $supportTicket): bool
    {
        return $user->hasRole('admin') || $this->canAccess($user, $supportTicket);
    }

    public function changeStatus(User $user, SupportTicket $supportTicket): bool
    {
        return $user->hasRole('admin') || $user->id === $supportTicket->assigned_to || $user->supplier?->id === $supportTicket->supplier_id;
    }

    public function assign(User $user, SupportTicket $supportTicket): bool
    {
        return $user->hasRole('admin');
    }

    private function canAccess(User $user, SupportTicket $supportTicket): bool
    {
        if ($user->hasRole('admin') || $supportTicket->requester_id === $user->id) {
            return true;
        }

        return $user->supplier?->id === $supportTicket->supplier_id;
    }
}

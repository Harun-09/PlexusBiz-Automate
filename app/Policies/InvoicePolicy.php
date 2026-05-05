<?php

namespace App\Policies;

use App\Domains\ECommerce\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Buyer can view their own invoices
        if ($user->hasRole('buyer')) {
            return $invoice->order->buyer_id === $user->id;
        }

        if ($user->hasRole('marketing_manager')) {
            return true;
        }

        // Supplier can view invoices for their products in the order
        if ($user->hasRole('supplier')) {
            return $invoice->order->items()
                ->where('supplier_id', $user->supplier?->id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('supplier');
    }

    public function download(User $user, Invoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }
}

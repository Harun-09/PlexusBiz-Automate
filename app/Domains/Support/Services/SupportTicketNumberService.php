<?php

namespace App\Domains\Support\Services;

use App\Domains\Support\Models\SupportTicket;

class SupportTicketNumberService
{
    public function next(): string
    {
        $prefix = 'TKT-'.now()->format('Ymd').'-';
        $lastId = (int) SupportTicket::query()->withTrashed()->max('id') + 1;

        return $prefix.str_pad((string) $lastId, 5, '0', STR_PAD_LEFT);
    }
}

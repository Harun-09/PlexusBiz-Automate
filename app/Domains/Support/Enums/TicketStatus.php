<?php

namespace App\Domains\Support\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Pending = 'pending';
    case WaitingSupplier = 'waiting_supplier';
    case Resolved = 'resolved';
    case Closed = 'closed';
}

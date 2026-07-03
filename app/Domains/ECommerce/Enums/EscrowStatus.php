<?php

namespace App\Domains\ECommerce\Enums;

enum EscrowStatus: string
{
    case Held = 'held';
    case Released = 'released';
    case Refunded = 'refunded';
}

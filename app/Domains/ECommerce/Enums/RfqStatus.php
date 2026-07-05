<?php

namespace App\Domains\ECommerce\Enums;

enum RfqStatus: string
{
    case Open = 'open';
    case Quoted = 'quoted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Converted = 'converted';
}

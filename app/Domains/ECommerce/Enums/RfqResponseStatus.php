<?php

namespace App\Domains\ECommerce\Enums;

enum RfqResponseStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';
}


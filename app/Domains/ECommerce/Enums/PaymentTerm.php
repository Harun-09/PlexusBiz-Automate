<?php

namespace App\Domains\ECommerce\Enums;

enum PaymentTerm: string
{
    case Cash = 'cash';
    case Net30 = 'net30';
    case Net60 = 'net60';
    case Bkash = 'bkash';
    case Nagad = 'nagad';
    case Rocket = 'rocket';
}

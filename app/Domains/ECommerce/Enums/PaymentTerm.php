<?php

namespace App\Domains\ECommerce\Enums;

enum PaymentTerm: string
{
    case Cash = 'cash';
    case Net30 = 'net30';
    case Net60 = 'net60';
}

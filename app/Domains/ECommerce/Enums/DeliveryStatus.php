<?php

namespace App\Domains\ECommerce\Enums;

enum DeliveryStatus: string
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
}

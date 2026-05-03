<?php

namespace App\Domains\ECommerce\Enums;

enum SupplierStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
}

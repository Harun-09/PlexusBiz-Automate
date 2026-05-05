<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Rejected = 'rejected';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}

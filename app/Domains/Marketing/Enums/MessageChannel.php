<?php

namespace App\Domains\Marketing\Enums;

enum MessageChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
    case System = 'system';
}

<?php

namespace App\Domains\Marketing\Enums;

enum MessageStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Read = 'read';
    case Failed = 'failed';
    case Skipped = 'skipped';
}

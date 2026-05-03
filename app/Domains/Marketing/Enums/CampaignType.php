<?php

namespace App\Domains\Marketing\Enums;

enum CampaignType: string
{
    case Email = 'email';
    case Sms = 'sms';
    case Mixed = 'mixed';
}

<?php

namespace App\Domains\CRM\Enums;

enum CustomerLifecycleStage: string
{
    case Lead = 'lead';
    case Prospect = 'prospect';
    case Customer = 'customer';
    case RepeatCustomer = 'repeat_customer';
    case AtRisk = 'at_risk';
}

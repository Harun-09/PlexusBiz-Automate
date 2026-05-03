<?php

namespace App\Domains\Workflow\Enums;

enum AutomationRuleStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

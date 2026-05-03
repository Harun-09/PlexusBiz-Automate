<?php

namespace App\Domains\Support\Enums;

enum SupportMessageVisibility: string
{
    case Public = 'public';
    case Internal = 'internal';
}

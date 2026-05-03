<?php

namespace App\Domains\Marketing\Contracts;

use App\Domains\Marketing\Data\DeliveryResult;

interface SmsProvider
{
    public function send(string $to, string $body, array $context = []): DeliveryResult;
}

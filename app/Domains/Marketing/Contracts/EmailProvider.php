<?php

namespace App\Domains\Marketing\Contracts;

use App\Domains\Marketing\Data\DeliveryResult;

interface EmailProvider
{
    public function send(string $to, string $subject, string $body, array $context = []): DeliveryResult;
}

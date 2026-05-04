<?php

return [
    'currency' => env('COMMERCE_CURRENCY', 'BDT'),
    'default_payment_gateway' => env('COMMERCE_DEFAULT_PAYMENT_GATEWAY', 'stripe'),
];

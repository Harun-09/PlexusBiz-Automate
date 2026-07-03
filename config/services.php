<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'api' => [
        'rate_limit_per_minute' => env('API_RATE_LIMIT_PER_MINUTE', 60),
    ],

    'stripe' => [
        'mode' => env('STRIPE_MODE', 'sandbox'),
        'sandbox_public_key' => env('STRIPE_SANDBOX_PUBLIC_KEY'),
        'sandbox_secret_key' => env('STRIPE_SANDBOX_SECRET_KEY'),
        'live_public_key' => env('STRIPE_LIVE_PUBLIC_KEY'),
        'live_secret_key' => env('STRIPE_LIVE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'bdt'),
        'display_name' => env('STRIPE_BRAND_DISPLAY_NAME', 'PlexusBiz'),
        'button_color' => env('STRIPE_BRAND_BUTTON_COLOR', '#1d4ed8'),
        'background_color' => env('STRIPE_BRAND_BACKGROUND_COLOR', '#f8fafc'),
        'border_style' => env('STRIPE_BRAND_BORDER_STYLE', 'rounded'),
        'font_family' => env('STRIPE_BRAND_FONT_FAMILY', 'inter'),
        'logo_url' => env('STRIPE_BRAND_LOGO_URL'),
        'icon_url' => env('STRIPE_BRAND_ICON_URL'),
    ],

    'sslcommerz' => [
        'sandbox' => (bool) env('SSLCOMMERZ_SANDBOX', true),
        'store_id' => env('SSLCOMMERZ_STORE_ID', ''),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', ''),
        'multi_card_name' => env('SSLCOMMERZ_MULTI_CARD_NAME', ''),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

];


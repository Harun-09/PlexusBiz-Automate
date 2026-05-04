<?php

return [
    'original_disk' => env('MEDIA_ORIGINAL_DISK', 'local'),
    'public_disk' => env('MEDIA_PUBLIC_DISK', 'public'),
    'product_root' => env('MEDIA_PRODUCT_ROOT', 'media/products'),
    'social_root' => env('MEDIA_SOCIAL_ROOT', 'media/social'),
    'original_segment' => env('MEDIA_ORIGINAL_SEGMENT', 'original'),
    'public_segment' => env('MEDIA_PUBLIC_SEGMENT', 'public'),
    'variants_segment' => env('MEDIA_VARIANTS_SEGMENT', 'variants'),
];

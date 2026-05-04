<?php

return [
    'original_disk' => env('MEDIA_ORIGINAL_DISK', 'local'),
    'public_disk' => env('MEDIA_PUBLIC_DISK', 'public'),
    'product_root' => env('MEDIA_PRODUCT_ROOT', 'media/products'),
    'social_root' => env('MEDIA_SOCIAL_ROOT', 'media/social'),
    'original_segment' => env('MEDIA_ORIGINAL_SEGMENT', 'original'),
    'public_segment' => env('MEDIA_PUBLIC_SEGMENT', 'public'),
    'variants_segment' => env('MEDIA_VARIANTS_SEGMENT', 'variants'),
    'thumbnail_max_width' => env('MEDIA_THUMBNAIL_MAX_WIDTH', 360),
    'thumbnail_max_height' => env('MEDIA_THUMBNAIL_MAX_HEIGHT', 360),
    'preview_max_width' => env('MEDIA_PREVIEW_MAX_WIDTH', 1280),
    'preview_max_height' => env('MEDIA_PREVIEW_MAX_HEIGHT', 720),
    'image_quality' => env('MEDIA_IMAGE_QUALITY', 86),
];

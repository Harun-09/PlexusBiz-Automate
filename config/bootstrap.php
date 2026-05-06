<?php

return [
    'once' => [
        'enabled' => filter_var(env('BOOTSTRAP_ON_FIRST_REQUEST', false), FILTER_VALIDATE_BOOLEAN),
        'marker_path' => env('BOOTSTRAP_ON_FIRST_REQUEST_MARKER', 'app/bootstrap-once.json'),
        'abort_on_failure' => filter_var(env('BOOTSTRAP_ON_FIRST_REQUEST_ABORT', true), FILTER_VALIDATE_BOOLEAN),
        'migrate_fresh' => filter_var(env('BOOTSTRAP_ON_FIRST_REQUEST_MIGRATE_FRESH', false), FILTER_VALIDATE_BOOLEAN),
        'seed' => filter_var(env('BOOTSTRAP_ON_FIRST_REQUEST_SEED', true), FILTER_VALIDATE_BOOLEAN),
        'storage_link_force' => filter_var(env('BOOTSTRAP_ON_FIRST_REQUEST_STORAGE_FORCE', true), FILTER_VALIDATE_BOOLEAN),
    ],
];

<?php

use App\Domains\Admin\AdminDomain;
use App\Domains\CRM\CRMDomain;
use App\Domains\ECommerce\ECommerceDomain;
use App\Domains\Marketing\MarketingDomain;
use App\Domains\Notifications\NotificationsDomain;
use App\Domains\Settings\SettingsDomain;
use App\Domains\Social\SocialDomain;
use App\Domains\Support\SupportDomain;
use App\Domains\Workflow\WorkflowDomain;

return [
    'modules' => [
        'admin' => [
            'class' => AdminDomain::class,
            'enabled' => env('MODULE_ADMIN_ENABLED', true),
            'locked' => true,
        ],
        'crm' => [
            'class' => CRMDomain::class,
            'enabled' => env('MODULE_CRM_ENABLED', true),
        ],
        'ecommerce' => [
            'class' => ECommerceDomain::class,
            'enabled' => env('MODULE_ECOMMERCE_ENABLED', true),
        ],
        'marketing' => [
            'class' => MarketingDomain::class,
            'enabled' => env('MODULE_MARKETING_ENABLED', true),
        ],
        'notifications' => [
            'class' => NotificationsDomain::class,
            'enabled' => env('MODULE_NOTIFICATIONS_ENABLED', true),
        ],
        'settings' => [
            'class' => SettingsDomain::class,
            'enabled' => env('MODULE_SETTINGS_ENABLED', true),
            'locked' => true,
        ],
        'social' => [
            'class' => SocialDomain::class,
            'enabled' => env('MODULE_SOCIAL_ENABLED', true),
        ],
        'support' => [
            'class' => SupportDomain::class,
            'enabled' => env('MODULE_SUPPORT_ENABLED', true),
        ],
        'workflow' => [
            'class' => WorkflowDomain::class,
            'enabled' => env('MODULE_WORKFLOW_ENABLED', true),
        ],
    ],
];

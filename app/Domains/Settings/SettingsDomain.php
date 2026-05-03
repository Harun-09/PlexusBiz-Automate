<?php

namespace App\Domains\Settings;

use App\Support\Domain\BaseDomainModule;

class SettingsDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'settings',
            name: 'Settings',
            routes: ['web,auth' => base_path('routes/domains/settings.php')],
        );
    }
}

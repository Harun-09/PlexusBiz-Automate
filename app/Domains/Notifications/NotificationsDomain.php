<?php

namespace App\Domains\Notifications;

use App\Support\Domain\BaseDomainModule;

class NotificationsDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'notifications',
            name: 'Notifications',
            routes: ['web,auth' => base_path('routes/domains/notifications.php')],
        );
    }
}

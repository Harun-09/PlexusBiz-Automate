<?php

namespace App\Domains\Support;

use App\Support\Domain\BaseDomainModule;

class SupportDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'support',
            name: 'Support Automation',
            routes: ['web,auth' => base_path('routes/domains/support.php')],
        );
    }
}

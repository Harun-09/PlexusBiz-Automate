<?php

namespace App\Domains\Social;

use App\Support\Domain\BaseDomainModule;

class SocialDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'social',
            name: 'Social Automation',
            routes: ['web,auth' => base_path('routes/domains/social.php')],
            providers: [SocialServiceProvider::class],
        );
    }
}

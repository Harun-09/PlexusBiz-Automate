<?php

namespace App\Domains\Marketing;

use App\Support\Domain\BaseDomainModule;

class MarketingDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'marketing',
            name: 'Marketing Automation',
            routes: ['web,auth' => base_path('routes/domains/marketing.php')],
            providers: [MarketingServiceProvider::class],
        );
    }
}

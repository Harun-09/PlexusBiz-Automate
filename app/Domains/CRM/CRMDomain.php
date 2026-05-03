<?php

namespace App\Domains\CRM;

use App\Support\Domain\BaseDomainModule;

class CRMDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'crm',
            name: 'CRM',
            routes: ['web,auth' => base_path('routes/domains/crm.php')],
        );
    }
}

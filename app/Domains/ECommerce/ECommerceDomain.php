<?php

namespace App\Domains\ECommerce;

use App\Support\Domain\BaseDomainModule;

class ECommerceDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'ecommerce',
            name: 'E-Commerce',
            routes: [
                'web' => base_path('routes/domains/marketplace.php'),
                'web,auth' => base_path('routes/domains/ecommerce.php'),
            ],
        );
    }
}

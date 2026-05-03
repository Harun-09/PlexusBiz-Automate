<?php

namespace App\Domains\Admin;

use App\Support\Domain\BaseDomainModule;

class AdminDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'admin',
            name: 'Admin',
            routes: ['web,auth' => base_path('routes/domains/admin.php')],
        );
    }
}

<?php

namespace App\Domains\Workflow;

use App\Support\Domain\BaseDomainModule;

class WorkflowDomain extends BaseDomainModule
{
    public function __construct()
    {
        parent::__construct(
            key: 'workflow',
            name: 'Workflow Automation',
            routes: ['web,auth' => base_path('routes/domains/workflow.php')],
        );
    }
}

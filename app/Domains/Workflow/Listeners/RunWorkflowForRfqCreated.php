<?php

namespace App\Domains\Workflow\Listeners;

use App\Domains\ECommerce\Events\RfqCreated;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Services\WorkflowEngineService;

class RunWorkflowForRfqCreated
{
    public function __construct(private readonly WorkflowEngineService $workflow)
    {
    }

    public function handle(RfqCreated $event): void
    {
        $this->workflow->handle(
            WorkflowTriggerEvent::RfqCreated->value,
            $event->payloadSnapshot(),
        );
    }
}

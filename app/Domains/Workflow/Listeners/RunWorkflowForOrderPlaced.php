<?php

namespace App\Domains\Workflow\Listeners;

use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Services\WorkflowEngineService;

class RunWorkflowForOrderPlaced
{
    public function __construct(private readonly WorkflowEngineService $engine)
    {
    }

    public function handle(OrderPlaced $event): void
    {
        $this->engine->handle(WorkflowTriggerEvent::OrderPlaced->value, $event->payloadSnapshot());
    }
}

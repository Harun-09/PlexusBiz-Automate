<?php

namespace App\Domains\Workflow\Listeners;

use App\Domains\ECommerce\Events\OrderStatusChanged;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Services\WorkflowEngineService;

class RunWorkflowForOrderStatusChanged
{
    public function __construct(private readonly WorkflowEngineService $workflow)
    {
    }

    public function handle(OrderStatusChanged $event): void
    {
        $this->workflow->handle(
            WorkflowTriggerEvent::OrderStatusChanged->value,
            $event->payloadSnapshot(),
        );
    }
}

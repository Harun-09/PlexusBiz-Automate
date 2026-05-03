<?php

namespace App\Domains\Workflow\Listeners;

use App\Domains\Support\Events\SupportTicketCreated;
use App\Domains\Workflow\Enums\WorkflowTriggerEvent;
use App\Domains\Workflow\Services\WorkflowEngineService;

class RunWorkflowForTicketCreated
{
    public function __construct(private readonly WorkflowEngineService $workflow)
    {
    }

    public function handle(SupportTicketCreated $event): void
    {
        $this->workflow->handle(
            WorkflowTriggerEvent::TicketCreated->value,
            $event->payloadSnapshot(),
        );
    }
}

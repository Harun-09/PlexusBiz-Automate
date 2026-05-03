<?php

namespace App\Domains\Workflow\Jobs;

use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Domains\Workflow\Services\WorkflowEngineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunWorkflowRuleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private readonly int $ruleId,
        private readonly string $triggerEvent,
        private readonly array $payload,
        private readonly ?int $logId = null,
    ) {
    }

    public function handle(WorkflowEngineService $engine): void
    {
        $engine->executeRule(
            AutomationRule::findOrFail($this->ruleId),
            $this->triggerEvent,
            $this->payload,
            $this->logId ? WorkflowLog::find($this->logId) : null,
        );
    }
}

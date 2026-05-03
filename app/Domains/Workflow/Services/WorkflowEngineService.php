<?php

namespace App\Domains\Workflow\Services;

use App\Domains\Workflow\Enums\AutomationRuleStatus;
use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Jobs\RunWorkflowRuleJob;
use App\Domains\Workflow\Models\AutomationRule;
use App\Domains\Workflow\Models\WorkflowLog;
use Illuminate\Support\Collection;
use Throwable;

class WorkflowEngineService
{
    public function __construct(
        private readonly ConditionEvaluator $conditions,
        private readonly WorkflowActionExecutor $actions,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, WorkflowLog>
     */
    public function handle(string $triggerEvent, array $payload): Collection
    {
        return AutomationRule::query()
            ->where('trigger_event', $triggerEvent)
            ->where('status', AutomationRuleStatus::Active->value)
            ->orderBy('priority')
            ->get()
            ->map(function (AutomationRule $rule) use ($triggerEvent, $payload): WorkflowLog {
                if (! $this->conditions->passes($rule->conditions_json, $payload)) {
                    return WorkflowLog::create([
                        'rule_id' => $rule->id,
                        'trigger_event' => $triggerEvent,
                        'payload' => $payload,
                        'status' => WorkflowLogStatus::Skipped,
                        'result' => ['condition_matched' => false],
                        'executed_at' => now(),
                    ]);
                }

                if ($rule->run_async) {
                    $log = WorkflowLog::create([
                        'rule_id' => $rule->id,
                        'trigger_event' => $triggerEvent,
                        'payload' => $payload,
                        'status' => WorkflowLogStatus::Running,
                        'result' => ['queued' => true],
                        'executed_at' => now(),
                    ]);

                    RunWorkflowRuleJob::dispatch($rule->id, $triggerEvent, $payload, $log->id);

                    return $log;
                }

                return $this->executeRule($rule, $triggerEvent, $payload);
            });
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function executeRule(AutomationRule $rule, string $triggerEvent, array $payload, ?WorkflowLog $log = null): WorkflowLog
    {
        $log ??= WorkflowLog::create([
            'rule_id' => $rule->id,
            'trigger_event' => $triggerEvent,
            'payload' => $payload,
            'status' => WorkflowLogStatus::Running,
            'executed_at' => now(),
        ]);

        try {
            $results = collect($rule->actions_json ?? [])
                ->map(fn (array $action): array => $this->actions->execute($action, $payload))
                ->values()
                ->all();

            $log->forceFill([
                'status' => WorkflowLogStatus::Success,
                'result' => [
                    'condition_matched' => true,
                    'actions' => $results,
                ],
                'error' => null,
                'executed_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $log->forceFill([
                'status' => WorkflowLogStatus::Failed,
                'error' => $exception->getMessage(),
                'result' => ['condition_matched' => true],
                'executed_at' => now(),
            ])->save();
        }

        return $log->refresh();
    }
}

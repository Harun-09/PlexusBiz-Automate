<?php

namespace App\Console\Commands;

use App\Domains\Workflow\Enums\WorkflowLogStatus;
use App\Domains\Workflow\Models\WorkflowLog;
use Illuminate\Console\Command;

class CloseStaleWorkflowRunsCommand extends Command
{
    protected $signature = 'workflow:close-stale-runs {--minutes=30 : Running logs older than this are marked failed}';

    protected $description = 'Marks stale running workflow logs as failed so scheduled workflow maintenance stays visible.';

    public function handle(): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $threshold = now()->subMinutes($minutes);
        $closedAt = now()->toIso8601String();

        $logs = WorkflowLog::query()
            ->where('status', WorkflowLogStatus::Running->value)
            ->where(function ($query) use ($threshold): void {
                $query->where('executed_at', '<=', $threshold)
                    ->orWhere(function ($query) use ($threshold): void {
                        $query->whereNull('executed_at')
                            ->where('created_at', '<=', $threshold);
                    });
            })
            ->get();

        $logs->each(function (WorkflowLog $log) use ($minutes, $closedAt): void {
            $result = $log->result ?? [];
            $result['stale_run_closed'] = [
                'after_minutes' => $minutes,
                'closed_at' => $closedAt,
            ];

            $log->forceFill([
                'status' => WorkflowLogStatus::Failed,
                'result' => $result,
                'error' => 'Workflow run timed out before the queue worker completed it.',
                'executed_at' => now(),
            ])->save();
        });

        $this->info("Closed {$logs->count()} stale workflow run(s).");

        return self::SUCCESS;
    }
}

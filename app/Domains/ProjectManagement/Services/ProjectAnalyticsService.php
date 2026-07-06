<?php

namespace App\Domains\ProjectManagement\Services;

use App\Domains\ProjectManagement\Models\Project;

class ProjectAnalyticsService
{
    /**
     * Calculate project health metrics: budget burn rate, schedule variance, and completion percentage.
     */
    public function getProjectHealth(Project $project)
    {
        $tasks = $project->tasks;
        $totalTasks = $tasks->count();

        if ($totalTasks === 0) {
            return [
                'completion_pct' => 0,
                'total_estimated_hours' => 0,
                'total_actual_hours' => 0,
                'schedule_variance' => 0,
                'health' => 'no_data',
            ];
        }

        $completedTasks = $tasks->where('status', 'done')->count();
        $completionPct = round(($completedTasks / $totalTasks) * 100, 2);

        $totalEstimated = $tasks->sum('estimated_hours');
        $totalActual = $tasks->sum('actual_hours');

        // Schedule Variance: negative = behind schedule
        $scheduleVariance = $totalEstimated - $totalActual;

        // Health determination
        $health = 'on_track';
        if ($totalActual > 0 && $totalEstimated > 0) {
            $efficiencyRatio = $totalActual / $totalEstimated;
            if ($efficiencyRatio > 1.2) {
                $health = 'at_risk';
            }
            if ($efficiencyRatio > 1.5) {
                $health = 'critical';
            }
        }

        return [
            'completion_pct' => $completionPct,
            'total_estimated_hours' => $totalEstimated,
            'total_actual_hours' => $totalActual,
            'schedule_variance' => $scheduleVariance,
            'health' => $health,
        ];
    }
}

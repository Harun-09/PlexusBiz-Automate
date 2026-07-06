<?php

namespace App\Domains\QualityControl\Services;

use App\Domains\QualityControl\Models\NonConformanceReport;
use App\Domains\QualityControl\Models\QualityInspection;

class InspectionService
{
    /**
     * Evaluate an inspection by checking all its criteria.
     *
     * If ALL criteria pass, set inspection status to 'passed'.
     * If ANY fail, set to 'failed' and auto-create a NonConformanceReport
     * with severity based on count of failures (1=minor, 2=major, 3+=critical).
     */
    public function evaluateInspection(QualityInspection $inspection): QualityInspection
    {
        $criteria = $inspection->criteria;

        $failedCriteria = $criteria->where('result', 'fail');
        $failureCount = $failedCriteria->count();

        if ($failureCount === 0) {
            $inspection->update(['status' => 'passed']);
        } else {
            $inspection->update(['status' => 'failed']);

            $severity = match (true) {
                $failureCount >= 3 => 'critical',
                $failureCount === 2 => 'major',
                default => 'minor',
            };

            $failedNames = $failedCriteria->pluck('criterion_name')->implode(', ');

            NonConformanceReport::create([
                'quality_inspection_id' => $inspection->id,
                'description' => "Inspection failed on: {$failedNames}",
                'severity' => $severity,
                'status' => 'open',
            ]);
        }

        return $inspection->refresh();
    }
}

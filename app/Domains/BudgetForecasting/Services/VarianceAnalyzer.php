<?php

namespace App\Domains\BudgetForecasting\Services;

use App\Domains\BudgetForecasting\Models\Budget;

class VarianceAnalyzer
{
    /**
     * Analyze budget variance for each line item.
     *
     * For each BudgetLineItem:
     * - variance = allocated_amount - spent_amount
     * - variance_pct = (variance / allocated_amount) * 100
     * - flag: 'overspent' if spent > allocated, 'warning' if spent > 90% of allocated, null otherwise
     *
     * @return array<int, array{
     *     line_item_id: int,
     *     category: string,
     *     allocated_amount: float,
     *     spent_amount: float,
     *     variance: float,
     *     variance_pct: float,
     *     flag: string|null
     * }>
     */
    public function analyzeBudgetVariance(Budget $budget): array
    {
        $budget->load('lineItems');

        $results = [];

        foreach ($budget->lineItems as $item) {
            $allocated = (float) $item->allocated_amount;
            $spent     = (float) $item->spent_amount;

            $variance    = $allocated - $spent;
            $variancePct = $allocated != 0
                ? ($variance / $allocated) * 100
                : 0;

            // Determine flag
            $flag = null;
            if ($spent > $allocated) {
                $flag = 'overspent';
            } elseif ($allocated > 0 && ($spent / $allocated) > 0.9) {
                $flag = 'warning';
            }

            $results[] = [
                'line_item_id'     => $item->id,
                'category'         => $item->category,
                'allocated_amount' => $allocated,
                'spent_amount'     => $spent,
                'variance'         => $variance,
                'variance_pct'     => round($variancePct, 2),
                'flag'             => $flag,
            ];
        }

        return $results;
    }
}

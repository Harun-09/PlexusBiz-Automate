<?php

namespace App\Domains\AssetManagement\Services;

use App\Domains\AssetManagement\Models\DepreciationSchedule;
use App\Domains\AssetManagement\Models\FixedAsset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DepreciationEngine
{
    /**
     * Calculate straight-line depreciation and generate monthly schedule entries.
     *
     * Monthly Depreciation = (purchase_cost - salvage_value) / (useful_life_years * 12)
     *
     * @param FixedAsset $asset
     * @return Collection<int, DepreciationSchedule>
     */
    public function calculateStraightLine(FixedAsset $asset): Collection
    {
        $totalMonths = $asset->useful_life_years * 12;
        $monthlyDepreciation = round(
            ($asset->purchase_cost - $asset->salvage_value) / $totalMonths,
            2
        );

        $accumulatedDepreciation = 0;
        $bookValue = (float) $asset->purchase_cost;
        $schedules = collect();
        $periodDate = Carbon::parse($asset->purchase_date)->startOfMonth();

        for ($month = 1; $month <= $totalMonths; $month++) {
            $periodDate = $periodDate->copy()->addMonth();

            // On the last month, adjust for any rounding remainder
            if ($month === $totalMonths) {
                $monthlyAmount = $bookValue - (float) $asset->salvage_value;
            } else {
                $monthlyAmount = $monthlyDepreciation;
            }

            $accumulatedDepreciation = round($accumulatedDepreciation + $monthlyAmount, 2);
            $bookValue = round($bookValue - $monthlyAmount, 2);

            $schedule = DepreciationSchedule::create([
                'fixed_asset_id' => $asset->id,
                'period_date' => $periodDate->toDateString(),
                'depreciation_amount' => $monthlyAmount,
                'accumulated_depreciation' => $accumulatedDepreciation,
                'book_value' => $bookValue,
            ]);

            $schedules->push($schedule);
        }

        return $schedules;
    }
}

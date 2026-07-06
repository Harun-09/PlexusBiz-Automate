<?php

namespace Tests\Feature;

use App\Domains\AssetManagement\Models\DepreciationSchedule;
use App\Domains\AssetManagement\Models\FixedAsset;
use App\Domains\AssetManagement\Services\DepreciationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DepreciationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
    }

    public function test_straight_line_monthly_depreciation_is_correct(): void
    {
        $asset = FixedAsset::create([
            'name' => 'Office Building',
            'category' => 'Real Estate',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 120000,
            'salvage_value' => 0,
            'useful_life_years' => 10,
            'status' => 'active',
        ]);

        $engine = new DepreciationEngine();
        $schedules = $engine->calculateStraightLine($asset);

        // Monthly depreciation = (120000 - 0) / (10 * 12) = 1000
        $this->assertEquals(1000, (float) $schedules->first()->depreciation_amount);
    }

    public function test_correct_number_of_schedule_records_created(): void
    {
        $asset = FixedAsset::create([
            'name' => 'Office Building',
            'category' => 'Real Estate',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 120000,
            'salvage_value' => 0,
            'useful_life_years' => 10,
            'status' => 'active',
        ]);

        $engine = new DepreciationEngine();
        $schedules = $engine->calculateStraightLine($asset);

        // 10 years * 12 months = 120 records
        $this->assertCount(120, $schedules);
        $this->assertEquals(120, DepreciationSchedule::where('fixed_asset_id', $asset->id)->count());
    }

    public function test_last_record_has_book_value_of_zero(): void
    {
        $asset = FixedAsset::create([
            'name' => 'Office Building',
            'category' => 'Real Estate',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 120000,
            'salvage_value' => 0,
            'useful_life_years' => 10,
            'status' => 'active',
        ]);

        $engine = new DepreciationEngine();
        $schedules = $engine->calculateStraightLine($asset);

        $lastSchedule = $schedules->last();
        $this->assertEquals(0, (float) $lastSchedule->book_value);
        $this->assertEquals(120000, (float) $lastSchedule->accumulated_depreciation);
    }
}

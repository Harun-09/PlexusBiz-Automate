<?php

namespace Tests\Feature;

use App\Domains\QualityControl\Models\InspectionCriteria;
use App\Domains\QualityControl\Models\QualityInspection;
use App\Domains\QualityControl\Services\InspectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InspectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private InspectionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();

        $this->service = new InspectionService();
    }

    private function createInspection(): QualityInspection
    {
        return QualityInspection::create([
            'product_id' => 1,
            'batch_number' => 'BATCH-001',
            'inspector_name' => 'John Doe',
            'inspection_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }

    public function test_all_criteria_pass_sets_inspection_to_passed_with_no_ncr(): void
    {
        $inspection = $this->createInspection();

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Weight',
            'expected_value' => '100g',
            'actual_value' => '100g',
            'result' => 'pass',
        ]);

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Dimensions',
            'expected_value' => '10x10cm',
            'actual_value' => '10x10cm',
            'result' => 'pass',
        ]);

        $result = $this->service->evaluateInspection($inspection);

        $this->assertEquals('passed', $result->status);
        $this->assertDatabaseMissing('non_conformance_reports', [
            'quality_inspection_id' => $inspection->id,
        ]);
    }

    public function test_one_criterion_fails_creates_ncr_with_minor_severity(): void
    {
        $inspection = $this->createInspection();

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Weight',
            'expected_value' => '100g',
            'actual_value' => '100g',
            'result' => 'pass',
        ]);

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Color',
            'expected_value' => 'Red',
            'actual_value' => 'Blue',
            'result' => 'fail',
        ]);

        $result = $this->service->evaluateInspection($inspection);

        $this->assertEquals('failed', $result->status);
        $this->assertDatabaseHas('non_conformance_reports', [
            'quality_inspection_id' => $inspection->id,
            'severity' => 'minor',
            'status' => 'open',
        ]);
    }

    public function test_three_criteria_fail_creates_ncr_with_critical_severity(): void
    {
        $inspection = $this->createInspection();

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Weight',
            'expected_value' => '100g',
            'actual_value' => '150g',
            'result' => 'fail',
        ]);

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Color',
            'expected_value' => 'Red',
            'actual_value' => 'Blue',
            'result' => 'fail',
        ]);

        InspectionCriteria::create([
            'quality_inspection_id' => $inspection->id,
            'criterion_name' => 'Texture',
            'expected_value' => 'Smooth',
            'actual_value' => 'Rough',
            'result' => 'fail',
        ]);

        $result = $this->service->evaluateInspection($inspection);

        $this->assertEquals('failed', $result->status);
        $this->assertDatabaseHas('non_conformance_reports', [
            'quality_inspection_id' => $inspection->id,
            'severity' => 'critical',
            'status' => 'open',
        ]);
    }
}

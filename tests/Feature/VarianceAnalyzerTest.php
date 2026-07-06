<?php

namespace Tests\Feature;

use App\Domains\BudgetForecasting\Models\Budget;
use App\Domains\BudgetForecasting\Models\BudgetLineItem;
use App\Domains\BudgetForecasting\Services\VarianceAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VarianceAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
    }

    protected function tearDown(): void
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }

    public function test_it_calculates_correct_variance_amounts(): void
    {
        $budget = Budget::create([
            'name'            => 'Q3 Operations',
            'fiscal_year'     => 2026,
            'department'      => 'Engineering',
            'total_allocated' => 30000.00,
            'status'          => 'approved',
        ]);

        // Under budget: 50% spent
        BudgetLineItem::create([
            'budget_id'        => $budget->id,
            'category'         => 'Software Licenses',
            'allocated_amount' => 10000.00,
            'spent_amount'     => 5000.00,
        ]);

        // Warning: 95% spent
        BudgetLineItem::create([
            'budget_id'        => $budget->id,
            'category'         => 'Cloud Hosting',
            'allocated_amount' => 10000.00,
            'spent_amount'     => 9500.00,
        ]);

        // Overspent: 110% spent
        BudgetLineItem::create([
            'budget_id'        => $budget->id,
            'category'         => 'Consulting',
            'allocated_amount' => 10000.00,
            'spent_amount'     => 11000.00,
        ]);

        $analyzer = new VarianceAnalyzer();
        $results  = $analyzer->analyzeBudgetVariance($budget);

        $this->assertCount(3, $results);

        // Under budget line item
        $this->assertEquals(5000.00, $results[0]['variance']);
        $this->assertEquals(50.00, $results[0]['variance_pct']);

        // Warning line item
        $this->assertEquals(500.00, $results[1]['variance']);
        $this->assertEquals(5.00, $results[1]['variance_pct']);

        // Overspent line item
        $this->assertEquals(-1000.00, $results[2]['variance']);
        $this->assertEquals(-10.00, $results[2]['variance_pct']);
    }

    public function test_it_assigns_correct_flags(): void
    {
        $budget = Budget::create([
            'name'            => 'Q4 Marketing',
            'fiscal_year'     => 2026,
            'total_allocated' => 30000.00,
            'status'          => 'draft',
        ]);

        // Under budget: 50% spent — no flag
        BudgetLineItem::create([
            'budget_id'        => $budget->id,
            'category'         => 'Ads',
            'allocated_amount' => 10000.00,
            'spent_amount'     => 5000.00,
        ]);

        // Warning: 95% spent
        BudgetLineItem::create([
            'budget_id'        => $budget->id,
            'category'         => 'Events',
            'allocated_amount' => 10000.00,
            'spent_amount'     => 9500.00,
        ]);

        // Overspent: 110% spent
        BudgetLineItem::create([
            'budget_id'        => $budget->id,
            'category'         => 'Branding',
            'allocated_amount' => 10000.00,
            'spent_amount'     => 11000.00,
        ]);

        $analyzer = new VarianceAnalyzer();
        $results  = $analyzer->analyzeBudgetVariance($budget);

        $this->assertNull($results[0]['flag'], 'Under-budget item should have no flag');
        $this->assertEquals('warning', $results[1]['flag'], '95% spent item should be flagged as warning');
        $this->assertEquals('overspent', $results[2]['flag'], '110% spent item should be flagged as overspent');
    }
}

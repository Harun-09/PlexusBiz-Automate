<?php

namespace Tests\Feature;

use App\Domains\Core\Services\OutboxService;
use App\Domains\HCM\Models\Employee;
use App\Domains\HCM\Services\PayrollEngine;
use App\Domains\HCM\Services\TaxCalculator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GratuityCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_gratuity_under_five_years()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-100',
            'basic_salary' => 50000,
            'joining_date' => now()->subYears(4), // 4 years
        ]);

        $engine = new PayrollEngine(new TaxCalculator(), new OutboxService());
        
        $gratuity = $engine->calculateGratuity($employee);
        $this->assertEquals(0.0, $gratuity);
    }

    public function test_gratuity_calculation_over_five_years()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-101',
            'basic_salary' => 50000,
            'joining_date' => now()->subYears(6), // 6 years
        ]);

        $engine = new PayrollEngine(new TaxCalculator(), new OutboxService());
        
        // 6 years * 50,000 = 300,000
        $gratuity = $engine->calculateGratuity($employee);
        $this->assertEquals(300000.0, $gratuity);
    }
}

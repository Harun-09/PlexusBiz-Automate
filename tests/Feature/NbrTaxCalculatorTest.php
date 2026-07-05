<?php

namespace Tests\Feature;

use App\Domains\HCM\Models\Employee;
use App\Domains\HCM\Services\TaxCalculator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NbrTaxCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_tax_below_exemption_limit()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-001',
            'basic_salary' => 20000,
            'joining_date' => now(),
            'is_female_or_senior' => false,
        ]);

        $calculator = new TaxCalculator();
        // Annual income 3,00,000 is below 3,50,000
        $tax = $calculator->calculateAnnualTax(300000, $employee);
        
        $this->assertEquals(0.0, $tax);
    }

    public function test_demographic_exemption_for_women()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-002',
            'basic_salary' => 30000,
            'joining_date' => now(),
            'is_female_or_senior' => true,
        ]);

        $calculator = new TaxCalculator();
        // Annual income 3,80,000 is below 4,00,000 (female limit)
        $tax = $calculator->calculateAnnualTax(380000, $employee);
        
        $this->assertEquals(0.0, $tax);
    }

    public function test_minimum_tax_rule()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-003',
            'basic_salary' => 30000,
            'joining_date' => now(),
        ]);

        $calculator = new TaxCalculator();
        // Annual income 3,60,000 (10,000 over 3,50,000 limit)
        // 10% of 10,000 = 1,000. 
        // But minimum tax is 5,000
        $tax = $calculator->calculateAnnualTax(360000, $employee);
        
        $this->assertEquals(5000.0, $tax);
    }

    public function test_progressive_slab_calculation()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-004',
            'basic_salary' => 100000, // 12,00,000 annual
            'joining_date' => now(),
        ]);

        $calculator = new TaxCalculator();
        // Annual income: 12,00,000
        // Exemption: 3,50,000
        // Taxable: 8,50,000
        // First 3,00,000 @ 10% = 30,000
        // Next 4,00,000 @ 15% = 60,000
        // Next 1,50,000 @ 20% = 30,000
        // Total Tax: 120,000
        $tax = $calculator->calculateAnnualTax(1200000, $employee);
        
        $this->assertEquals(120000.0, $tax);
    }
}

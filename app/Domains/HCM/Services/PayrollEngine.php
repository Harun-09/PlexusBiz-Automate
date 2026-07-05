<?php

namespace App\Domains\HCM\Services;

use App\Domains\HCM\Models\Employee;
use App\Domains\HCM\Models\PayrollSlip;
use App\Domains\Core\Services\OutboxService;
use App\Domains\HCM\Events\EmployeePaid;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollEngine
{
    protected TaxCalculator $taxCalculator;
    protected OutboxService $outboxService;

    public function __construct(TaxCalculator $taxCalculator, OutboxService $outboxService)
    {
        $this->taxCalculator = $taxCalculator;
        $this->outboxService = $outboxService;
    }

    /**
     * Generate payroll for an employee for a specific month.
     */
    public function generatePayroll(Employee $employee, string $monthYear): PayrollSlip
    {
        // Prevent duplicate generation
        $existing = PayrollSlip::where('employee_id', $employee->id)
            ->where('month_year', $monthYear)
            ->first();

        if ($existing) {
            return $existing;
        }

        // According to BD conventions, basic is usually 50-60% of gross.
        // We will assume 'basic_salary' on the employee is the core basic pay.
        $basicPay = (float) $employee->basic_salary;
        $houseRent = $basicPay * 0.50; // Common structure: 50% of basic
        $medicalAllowance = $basicPay * 0.10; // Common structure: 10% of basic
        
        $grossPay = $basicPay + $houseRent + $medicalAllowance;

        // Provident Fund (Mandatory deduction)
        $pfDeducted = 0.0;
        if ($employee->is_pf_active) {
            $pfDeducted = $basicPay * ($employee->pf_percentage / 100);
        }

        // Tax Calculation (Annualized, then divided by 12)
        // Note: For actual NBR rules, some allowances have exemptions (e.g. House Rent up to 300k or 50% of basic).
        // For this engine, we will assume 100% of basic and house rent above a threshold is taxable.
        // To simplify for this blueprint, we use Gross Pay * 12.
        $annualTaxableIncome = $grossPay * 12;
        $annualTax = $this->taxCalculator->calculateAnnualTax($annualTaxableIncome, $employee);
        $taxDeducted = round($annualTax / 12, 2);

        $netPay = $grossPay - $pfDeducted - $taxDeducted;

        return PayrollSlip::create([
            'employee_id' => $employee->id,
            'month_year' => $monthYear,
            'basic_pay' => $basicPay,
            'house_rent' => $houseRent,
            'medical_allowance' => $medicalAllowance,
            'gross_pay' => $grossPay,
            'tax_deducted' => $taxDeducted,
            'pf_deducted' => $pfDeducted,
            'net_pay' => $netPay,
            'is_disbursed' => false,
        ]);
    }

    /**
     * Calculate statutory gratuity based on BD Labour Act (Final Basic * Years of Service).
     * Usually applies if the employee has served > 5 years.
     */
    public function calculateGratuity(Employee $employee): float
    {
        $yearsOfService = (int) floor(Carbon::parse($employee->joining_date)->diffInYears(now()));
        
        if ($yearsOfService < 5) {
            return 0.0; // Not eligible for gratuity under BD law (usually 5+ years required)
        }

        return (float) $employee->basic_salary * $yearsOfService;
    }

    /**
     * Disburse payroll and trigger Finance Ledger event.
     */
    public function disbursePayroll(PayrollSlip $slip)
    {
        if ($slip->is_disbursed) {
            throw new \Exception("Payroll slip already disbursed.");
        }

        DB::transaction(function () use ($slip) {
            $slip->update([
                'is_disbursed' => true,
                'disbursed_at' => now(),
            ]);

            // Save the event to the outbox for the Finance domain to process
            $this->outboxService->saveEvent(EmployeePaid::class, [
                'payroll_slip_id' => $slip->id,
                'employee_code' => $slip->employee->employee_code,
                'gross_pay' => $slip->gross_pay,
                'net_pay' => $slip->net_pay,
                'pf_deducted' => $slip->pf_deducted,
                'tax_deducted' => $slip->tax_deducted,
                'month_year' => $slip->month_year,
            ]);
        });
    }
}

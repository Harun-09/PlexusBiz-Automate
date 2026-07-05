<?php

namespace App\Domains\Finance\Listeners;

use App\Domains\HCM\Events\EmployeePaid;
use App\Domains\Finance\Services\LedgerService;
use App\Domains\Finance\Models\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RecordPayrollLedger implements ShouldQueue
{
    use InteractsWithQueue;

    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeePaid $event): void
    {
        $payload = $event->payload;
        $idempotencyKey = 'PAYROLL_' . $payload['payroll_slip_id'];

        try {
            // Retrieve necessary accounts (Assuming they exist or seeding them later)
            // For now, we will create/find them on the fly for robust testing
            $salaryExpense = Account::firstOrCreate(
                ['code' => '5001'],
                ['name' => 'Salary Expense', 'type' => 'expense', 'normal_balance' => 'debit']
            );
            
            $pfExpense = Account::firstOrCreate(
                ['code' => '5002'],
                ['name' => 'Provident Fund Expense', 'type' => 'expense', 'normal_balance' => 'debit']
            );
            
            $cashAccount = Account::firstOrCreate(
                ['code' => '1001'],
                ['name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']
            );
            
            $taxLiability = Account::firstOrCreate(
                ['code' => '2001'],
                ['name' => 'Tax Payable (NBR)', 'type' => 'liability', 'normal_balance' => 'credit']
            );
            
            $pfLiability = Account::firstOrCreate(
                ['code' => '2002'],
                ['name' => 'Provident Fund Payable', 'type' => 'liability', 'normal_balance' => 'credit']
            );

            // Double Entry Calculation
            // Debit: Salary Expense (Gross Pay)
            // Debit: PF Expense (Employer's matching contribution, usually equals employee's)
            // Credit: Cash (Net Pay to employee)
            // Credit: Tax Liability (Tax deducted)
            // Credit: PF Liability (Employee deduction + Employer match = 2 * PF Deducted)
            
            $employerPfMatch = (float) $payload['pf_deducted'];
            $totalPfLiability = $payload['pf_deducted'] + $employerPfMatch;

            $postings = [
                ['account_id' => $salaryExpense->id, 'type' => 'debit', 'amount' => $payload['gross_pay']],
            ];

            if ($employerPfMatch > 0) {
                $postings[] = ['account_id' => $pfExpense->id, 'type' => 'debit', 'amount' => $employerPfMatch];
                $postings[] = ['account_id' => $pfLiability->id, 'type' => 'credit', 'amount' => $totalPfLiability];
            }

            if ($payload['tax_deducted'] > 0) {
                $postings[] = ['account_id' => $taxLiability->id, 'type' => 'credit', 'amount' => $payload['tax_deducted']];
            }

            $postings[] = ['account_id' => $cashAccount->id, 'type' => 'credit', 'amount' => $payload['net_pay']];

            // Record the balanced entry
            $this->ledgerService->recordEntry(
                $idempotencyKey,
                "Payroll Disbursed for Employee {$payload['employee_code']} ({$payload['month_year']})",
                $postings,
                "PR-SLIP-" . $payload['payroll_slip_id']
            );

        } catch (\Exception $e) {
            Log::error("Failed to record payroll in ledger for slip {$payload['payroll_slip_id']}", ['error' => $e->getMessage()]);
            throw $e; // Re-throw to fail the job if needed
        }
    }
}

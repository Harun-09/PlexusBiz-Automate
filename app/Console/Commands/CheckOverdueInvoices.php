<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Enums\PaymentTerm;
use Illuminate\Support\Carbon;

class CheckOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-overdue-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for overdue net-term orders and applies soft blocks or late fees.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue invoices...');

        // Get orders that are net terms, not fully paid, and have a due date in the past
        $overdueOrders = Order::with('customer')
            ->whereIn('payment_term', [PaymentTerm::Net30->value, PaymentTerm::Net60->value])
            ->where('payment_status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueOrders as $order) {
            $daysOverdue = Carbon::parse($order->due_date)->diffInDays(now());
            $customer = $order->customer;

            if (! $customer) {
                continue;
            }

            if ($daysOverdue > 30) {
                // Apply Late Fee (e.g., 3%) if not already applied today
                $lateFee = round($order->subtotal * 0.03, 2);
                
                // For simplicity, we just set the late fee amount. In reality, you'd add this to the total and create a line item or update invoice.
                if ($order->late_fee_amount < $lateFee) {
                    $order->forceFill([
                        'late_fee_amount' => $lateFee,
                        'grand_total' => $order->grand_total + ($lateFee - $order->late_fee_amount)
                    ])->save();
                    $this->info("Applied late fee to Order #{$order->order_number}");
                }

                // Ensure customer is restricted
                if (! $customer->is_credit_restricted) {
                    $customer->forceFill(['is_credit_restricted' => true])->save();
                }
            } elseif ($daysOverdue > 7) {
                // Soft Block (Restrict Account)
                if (! $customer->is_credit_restricted) {
                    $customer->forceFill(['is_credit_restricted' => true])->save();
                    $this->info("Restricted account for Customer #{$customer->id} due to Order #{$order->order_number}");
                }
            } else {
                // Grace Period (1-7 days)
                $this->info("Order #{$order->order_number} is in Grace Period ({$daysOverdue} days). Send reminder.");
                // TODO: Dispatch Email/Notification Job here
            }
        }

        $this->info('Overdue invoices check completed.');
    }
}

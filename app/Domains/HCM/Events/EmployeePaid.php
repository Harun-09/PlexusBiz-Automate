<?php

namespace App\Domains\HCM\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeePaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    /**
     * Create a new event instance.
     *
     * @param array $payload Must contain: payroll_slip_id, employee_code, gross_pay, net_pay, pf_deducted, tax_deducted, month_year
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}

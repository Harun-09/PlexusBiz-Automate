<?php

namespace App\Domains\CRM\Services;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Enums\LeadStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Models\Lead;
use Illuminate\Support\Facades\DB;

class LeadConversionService
{
    public function convert(Lead $lead): Customer
    {
        return DB::transaction(function () use ($lead): Customer {
            $customer = Customer::firstOrCreate(
                ['email' => $lead->email],
                [
                    'contact_name' => $lead->contact_name,
                    'company_name' => $lead->company_name,
                    'phone' => $lead->phone,
                    'status' => CustomerStatus::Active,
                    'lifecycle_stage' => CustomerLifecycleStage::Prospect,
                    'tags' => ['converted-lead'],
                    'last_activity_at' => now(),
                ],
            );

            $lead->forceFill([
                'customer_id' => $customer->id,
                'status' => LeadStatus::Converted,
                'converted_at' => now(),
            ])->save();

            return $customer;
        });
    }
}

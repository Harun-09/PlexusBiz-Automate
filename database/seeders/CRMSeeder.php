<?php

namespace Database\Seeders;

use App\Domains\CRM\Enums\InteractionType;
use App\Domains\CRM\Enums\LeadStatus;
use App\Domains\CRM\Models\CustomerSegment;
use App\Domains\CRM\Models\Lead;
use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\CRM\Services\InteractionLogger;
use App\Models\User;
use Illuminate\Database\Seeder;

class CRMSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();

        $customer = app(CustomerProfileService::class)->ensureForUser($buyer, [
            'company_name' => 'Plexus Buyer Company',
            'tags' => ['wholesale', 'priority'],
        ]);

        Lead::updateOrCreate(
            ['email' => 'procurement@example.com'],
            [
                'assigned_user_id' => $marketing->id,
                'source' => 'website',
                'status' => LeadStatus::Qualified,
                'company_name' => 'Procurement Group',
                'contact_name' => 'Procurement Lead',
                'phone' => '+8801711111111',
                'value' => '750000.00',
                'notes' => 'Interested in recurring industrial equipment supply.',
                'next_follow_up_at' => now()->addDays(3),
            ],
        );

        CustomerSegment::updateOrCreate(
            ['slug' => 'priority-wholesale'],
            [
                'name' => 'Priority Wholesale',
                'status' => 'active',
                'description' => 'Wholesale buyers tagged for priority account management.',
                'filters_json' => [
                    'status' => 'active',
                    'tags' => ['wholesale', 'priority'],
                ],
            ],
        );

        if ($customer->interactions()->doesntExist()) {
            app(InteractionLogger::class)->record(
                customer: $customer,
                type: InteractionType::Note,
                summary: 'Seeded CRM profile for buyer account.',
                actor: $marketing,
                direction: 'internal',
            );
        }
    }
}

<?php

namespace App\Listeners\Marketing;

use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\Marketing\Services\MarketingTriggerService;
use Illuminate\Auth\Events\Registered;

class SendMarketingWelcomeEmail
{
    public function __construct(
        private readonly CustomerProfileService $customers,
        private readonly MarketingTriggerService $marketing,
    ) {
    }

    public function handle(Registered $event): void
    {
        $customer = $this->customers->ensureForUser($event->user, [
            'contact_name' => $event->user->name,
            'email' => $event->user->email,
        ]);

        $this->marketing->welcomeCustomer($customer);
    }
}

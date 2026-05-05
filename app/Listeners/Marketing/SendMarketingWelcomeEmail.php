<?php

namespace App\Listeners\Marketing;

use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\Marketing\Services\MarketingTriggerService;
use App\Enums\RoleName;
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
        if ((string) $event->user->account_type !== RoleName::Buyer->value && ! $event->user->hasRole(RoleName::Buyer->value)) {
            return;
        }

        $customer = $this->customers->ensureForUser($event->user, [
            'contact_name' => $event->user->name,
            'email' => $event->user->email,
        ]);

        $this->marketing->welcomeCustomer($customer);
    }
}

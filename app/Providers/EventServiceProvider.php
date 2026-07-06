<?php

namespace App\Providers;

use App\Domains\ECommerce\Events\OrderPlaced;
use App\Domains\ECommerce\Events\OrderStatusChanged;
use App\Domains\ECommerce\Events\RfqCreated;
use App\Domains\Support\Events\SupportTicketCreated;
use App\Listeners\Marketing\SendMarketingWelcomeEmail;
use App\Listeners\Marketing\SendOrderConfirmationEmail;
use App\Domains\Workflow\Listeners\RunWorkflowForOrderPlaced;
use App\Domains\Workflow\Listeners\RunWorkflowForOrderStatusChanged;
use App\Domains\Workflow\Listeners\RunWorkflowForRfqCreated;
use App\Domains\Workflow\Listeners\RunWorkflowForTicketCreated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            SendMarketingWelcomeEmail::class,
        ],
        OrderPlaced::class => [
            RunWorkflowForOrderPlaced::class,
            SendOrderConfirmationEmail::class,
            \App\Domains\Tax\Listeners\GenerateTaxInvoice::class,
        ],
        OrderStatusChanged::class => [
            RunWorkflowForOrderStatusChanged::class,
        ],
        RfqCreated::class => [
            RunWorkflowForRfqCreated::class,
        ],
        SupportTicketCreated::class => [
            RunWorkflowForTicketCreated::class,
        ],
        \App\Domains\HCM\Events\EmployeePaid::class => [
            \App\Domains\Finance\Listeners\RecordPayrollLedger::class,
        ],
        \App\Domains\Tax\Events\VatRecorded::class => [
            \App\Domains\Finance\Listeners\RecordVatLedger::class,
        ],
        \App\Domains\Tax\Events\PurchaseRecorded::class => [
            \App\Domains\Tax\Listeners\RecordPurchaseVat::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

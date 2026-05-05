<?php

namespace App\Providers;

use App\Domains\CRM\Models\Customer;
use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Product;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Support\Models\SupportTicket;
use App\Domains\Workflow\Models\WorkflowLog;
use App\Policies\CampaignPolicy;
use App\Policies\CampaignTemplatePolicy;
use App\Policies\CustomerPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SocialPostPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\WorkflowLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Campaign::class => CampaignPolicy::class,
        CampaignTemplate::class => CampaignTemplatePolicy::class,
        Customer::class => CustomerPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Order::class => OrderPolicy::class,
        Product::class => ProductPolicy::class,
        SocialPost::class => SocialPostPolicy::class,
        SupportTicket::class => SupportTicketPolicy::class,
        WorkflowLog::class => WorkflowLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}

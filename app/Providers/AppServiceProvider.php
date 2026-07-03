<?php

namespace App\Providers;

use App\Domains\Marketing\Contracts\SmsProvider;
use App\Domains\Marketing\Providers\MockSmsProvider;
use App\Repositories\ECommerce\EloquentProductRepository;
use App\Repositories\ECommerce\EloquentRfqResponseRepository;
use App\Repositories\ECommerce\ProductRepositoryInterface;
use App\Repositories\ECommerce\RfqResponseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsProvider::class, MockSmsProvider::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(RfqResponseRepositoryInterface::class, EloquentRfqResponseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Domains\ECommerce\Models\Order::observe(\App\Observers\AuditTrailObserver::class);
        \App\Domains\ECommerce\Models\Invoice::observe(\App\Observers\AuditTrailObserver::class);
        \App\Domains\ECommerce\Models\Rfq::observe(\App\Observers\AuditTrailObserver::class);
        \App\Domains\ECommerce\Models\RfqResponse::observe(\App\Observers\AuditTrailObserver::class);
    }
}

<?php

namespace App\Domains\Marketing;

use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Providers\LaravelEmailProvider;
use Illuminate\Support\ServiceProvider;

class MarketingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmailProvider::class, LaravelEmailProvider::class);
    }
}

<?php

namespace App\Domains\Marketing;

use App\Domains\Marketing\Contracts\EmailProvider;
use App\Domains\Marketing\Contracts\SmsProvider;
use App\Domains\Marketing\Providers\MockEmailProvider;
use App\Domains\Marketing\Providers\MockSmsProvider;
use Illuminate\Support\ServiceProvider;

class MarketingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmailProvider::class, MockEmailProvider::class);
        $this->app->bind(SmsProvider::class, MockSmsProvider::class);
    }
}

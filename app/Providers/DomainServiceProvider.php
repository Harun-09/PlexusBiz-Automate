<?php

namespace App\Providers;

use App\Support\Domain\DomainRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DomainRegistry::class);

        (new DomainRegistry($this->app['config']))->all()->each(function ($module): void {
            foreach ($module->serviceProviders() as $provider) {
                $this->app->register($provider);
            }
        });
    }

    public function boot(DomainRegistry $registry): void
    {
        $registry->enabled()->each(function ($module): void {
            foreach ($module->routeFiles() as $middleware => $routeFile) {
                if (! file_exists($routeFile)) {
                    continue;
                }

                Route::middleware(explode(',', $middleware))->group($routeFile);
            }
        });
    }
}

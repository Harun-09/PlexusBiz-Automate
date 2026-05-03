<?php

namespace App\Support\Domain;

use App\Contracts\DomainModule;

abstract class BaseDomainModule implements DomainModule
{
    /**
     * @param array<string, string> $routes
     * @param array<int, class-string<\Illuminate\Support\ServiceProvider>> $providers
     */
    public function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly array $routes = [],
        private readonly array $providers = [],
        private readonly bool $enabledByDefault = true,
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function enabledByDefault(): bool
    {
        return $this->enabledByDefault;
    }

    public function serviceProviders(): array
    {
        return $this->providers;
    }

    public function routeFiles(): array
    {
        return $this->routes;
    }
}

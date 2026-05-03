<?php

namespace App\Contracts;

interface DomainModule
{
    public function key(): string;

    public function name(): string;

    public function enabledByDefault(): bool;

    /**
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    public function serviceProviders(): array;

    /**
     * @return array<string, string>
     */
    public function routeFiles(): array;
}

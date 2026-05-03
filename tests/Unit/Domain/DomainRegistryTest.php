<?php

namespace Tests\Unit\Domain;

use App\Contracts\DomainModule;
use App\Support\Domain\DomainRegistry;
use Tests\TestCase;

class DomainRegistryTest extends TestCase
{
    public function test_it_loads_all_registered_domain_modules(): void
    {
        $registry = app(DomainRegistry::class);

        $modules = $registry->all();

        $this->assertCount(9, $modules);
        $this->assertContainsOnlyInstancesOf(DomainModule::class, $modules);
        $this->assertSame(
            ['admin', 'crm', 'ecommerce', 'marketing', 'notifications', 'settings', 'social', 'support', 'workflow'],
            $modules->map->key()->all()
        );
    }

    public function test_domain_keys_are_unique(): void
    {
        $keys = app(DomainRegistry::class)->all()->map->key();

        $this->assertSame($keys->count(), $keys->unique()->count());
    }
}

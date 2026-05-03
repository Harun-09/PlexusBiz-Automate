<?php

namespace App\Support\Domain;

use App\Contracts\DomainModule;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class DomainRegistry
{
    public function __construct(private readonly ConfigRepository $config)
    {
    }

    /**
     * @return Collection<int, DomainModule>
     */
    public function all(): Collection
    {
        return collect($this->config->get('domains.modules', []))
            ->map(fn (array $definition): DomainModule => $this->buildModule($definition))
            ->values();
    }

    /**
     * @return Collection<int, DomainModule>
     */
    public function enabled(): Collection
    {
        return collect($this->config->get('domains.modules', []))
            ->filter(fn (array $definition): bool => (bool) ($definition['enabled'] ?? true))
            ->map(fn (array $definition): DomainModule => $this->buildModule($definition))
            ->values();
    }

    /**
     * @param array{class?: class-string} $definition
     */
    private function buildModule(array $definition): DomainModule
    {
        $class = $definition['class'] ?? null;

        if (! is_string($class) || ! class_exists($class)) {
            throw new InvalidArgumentException('Domain module class is missing or unavailable.');
        }

        $module = app($class);

        if (! $module instanceof DomainModule) {
            throw new InvalidArgumentException(sprintf('%s must implement %s.', $class, DomainModule::class));
        }

        return $module;
    }
}

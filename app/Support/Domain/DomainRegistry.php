<?php

namespace App\Support\Domain;

use App\Contracts\DomainModule;
use App\Domains\Settings\Models\ModuleSetting;
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
        $overrides = ModuleSetting::enabledMap();

        return collect($this->config->get('domains.modules', []))
            ->filter(fn (array $definition, string $key): bool => $this->isEnabled($key, $definition, $overrides))
            ->map(fn (array $definition): DomainModule => $this->buildModule($definition))
            ->values();
    }

    /**
     * @param array<string, bool> $overrides
     */
    private function isEnabled(string $key, array $definition, array $overrides): bool
    {
        if ((bool) ($definition['locked'] ?? false)) {
            return true;
        }

        if (array_key_exists($key, $overrides)) {
            return (bool) $overrides[$key];
        }

        return (bool) ($definition['enabled'] ?? true);
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

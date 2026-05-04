<?php

namespace App\Http\Controllers\Settings;

use App\Domains\Settings\Models\ModuleSetting;
use App\Http\Controllers\Controller;
use App\Support\Audit\AuditLogger;
use App\Support\Domain\DomainRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModuleSettingsController extends Controller
{
    public function index(DomainRegistry $registry): Response
    {
        $definitions = config('domains.modules', []);
        $moduleMap = $registry->all()->keyBy->key();
        $overrides = ModuleSetting::enabledMap();

        $modules = collect($definitions)
            ->map(function (array $definition, string $key) use ($moduleMap, $overrides): array {
                $locked = (bool) ($definition['locked'] ?? false);
                $hasOverride = array_key_exists($key, $overrides);
                $enabled = $locked ? true : ($hasOverride ? (bool) $overrides[$key] : (bool) ($definition['enabled'] ?? true));
                $module = $moduleMap->get($key);

                return [
                    'key' => $key,
                    'name' => $module?->name() ?? str_replace('_', ' ', ucfirst($key)),
                    'description' => $this->descriptionFor($key),
                    'enabled' => $enabled,
                    'default_enabled' => (bool) ($definition['enabled'] ?? true),
                    'locked' => $locked,
                    'source' => $locked
                        ? 'Locked on'
                        : ($hasOverride ? 'Database override' : 'Env default'),
                    'override' => $hasOverride,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('Settings/Modules/Index', [
            'modules' => $modules,
            'summary' => $this->summaryFor($modules),
        ]);
    }

    public function update(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'modules' => ['required', 'array'],
            'modules.*' => ['nullable', 'boolean'],
        ]);

        $definitions = config('domains.modules', []);
        $beforeMap = ModuleSetting::enabledMap();
        $beforeSnapshot = $this->moduleSnapshot($definitions, $beforeMap);
        $changedModules = [];

        foreach ($definitions as $key => $definition) {
            if ((bool) ($definition['locked'] ?? false)) {
                continue;
            }

            $enabled = (bool) data_get($validated, 'modules.'.$key, false);
            $current = array_key_exists($key, $beforeMap)
                ? (bool) $beforeMap[$key]
                : (bool) ($definition['enabled'] ?? true);

            ModuleSetting::setEnabled($key, $enabled);

            if ($current !== $enabled) {
                $changedModules[] = $key;
            }
        }

        if ($changedModules !== []) {
            $auditLogger->record(
                actor: $request->user(),
                moduleKey: 'settings',
                action: 'settings.modules.updated',
                description: 'Module settings updated.',
                subjectLabel: 'Module settings',
                before: $beforeSnapshot,
                after: $this->moduleSnapshot($definitions, ModuleSetting::enabledMap()),
                metadata: [
                    'changed_modules' => $changedModules,
                ],
            );
        }

        return back()->with('success', 'Module settings updated successfully.');
    }

    /**
     * @param array<int, array<string, mixed>> $modules
     * @return array{total: int, enabled: int, disabled: int, locked: int, overrides: int}
     */
    private function summaryFor(array $modules): array
    {
        return [
            'total' => count($modules),
            'enabled' => count(array_filter($modules, fn (array $module): bool => (bool) $module['enabled'])),
            'disabled' => count(array_filter($modules, fn (array $module): bool => ! (bool) $module['enabled'])),
            'locked' => count(array_filter($modules, fn (array $module): bool => (bool) $module['locked'])),
            'overrides' => count(array_filter($modules, fn (array $module): bool => (bool) $module['override'])),
        ];
    }

    private function descriptionFor(string $key): string
    {
        return match ($key) {
            'admin' => 'Platform control center, users, suppliers, and operational KPIs.',
            'crm' => 'Customer records, lifecycle stages, and relationship context.',
            'ecommerce' => 'Products, carts, orders, invoices, and payment flows.',
            'marketing' => 'Campaigns, promotions, outreach, and delivery history.',
            'notifications' => 'In-app and delivery notifications across the workspace.',
            'settings' => 'Admin control panel for platform-level configuration.',
            'social' => 'Social publishing, scheduling, and content calendar tools.',
            'support' => 'Buyer and supplier support tickets with messages and SLA tracking.',
            'workflow' => 'Automation rules, execution logs, and error tracking.',
            default => 'Domain module toggle controlled from the admin console.',
        };
    }

    /**
     * @param array<string, array<string, mixed>> $definitions
     * @param array<string, bool> $overrides
     * @return array<string, bool>
     */
    private function moduleSnapshot(array $definitions, array $overrides): array
    {
        $snapshot = [];

        foreach ($definitions as $key => $definition) {
            if ((bool) ($definition['locked'] ?? false)) {
                continue;
            }

            $snapshot[$key] = array_key_exists($key, $overrides)
                ? (bool) $overrides[$key]
                : (bool) ($definition['enabled'] ?? true);
        }

        return $snapshot;
    }
}

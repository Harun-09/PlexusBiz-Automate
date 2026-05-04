<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Support\Domain\DomainRegistry;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ModuleSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_module_settings_page(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/settings/modules')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Settings/Modules/Index')
                ->where('modules.0.key', 'admin')
                ->where('modules.0.locked', true)
                ->where('modules.5.key', 'settings')
                ->where('modules.5.locked', true)
                ->where('summary.total', 9));
    }

    public function test_admin_can_toggle_module_settings_and_registry_reflects_overrides(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)->patch('/settings/modules', [
            'modules' => [
                'admin' => false,
                'crm' => true,
                'ecommerce' => false,
                'marketing' => true,
                'notifications' => true,
                'settings' => false,
                'social' => true,
                'support' => true,
                'workflow' => true,
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('module_settings', [
            'module_key' => 'ecommerce',
            'enabled' => false,
        ]);

        $this->assertDatabaseHas('module_settings', [
            'module_key' => 'marketing',
            'enabled' => true,
        ]);

        $enabledKeys = app(DomainRegistry::class)->enabled()->map->key()->all();

        $this->assertContains('admin', $enabledKeys);
        $this->assertContains('settings', $enabledKeys);
        $this->assertNotContains('ecommerce', $enabledKeys);
    }
}

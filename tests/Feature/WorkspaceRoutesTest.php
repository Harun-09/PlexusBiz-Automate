<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WorkspaceRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_workspace_is_role_protected(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($buyer)->get('/admin')->assertForbidden();
    }

    public function test_role_workspaces_render_for_assigned_users(): void
    {
        $this->seed(RbacSeeder::class);

        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();
        $supplier = User::where('email', 'supplier@plexus.test')->firstOrFail();
        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();

        $this->actingAs($buyer)->get('/marketplace')->assertOk();
        $this->actingAs($supplier)->get('/commerce/products')->assertOk();
        $this->actingAs($marketing)->get('/marketing/campaigns')->assertOk();
        $this->actingAs($marketing)->get('/workflow/logs')->assertOk();
    }

    public function test_workspace_tables_support_search_and_status_filters(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/users?search=buyer&status=active')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Workspace/Index')
                ->where('workspace.filters.search', 'buyer')
                ->where('workspace.filters.status', 'active')
                ->has('workspace.rows', 1)
                ->where('workspace.rows.0.Email', 'buyer@plexus.test'));
    }
}

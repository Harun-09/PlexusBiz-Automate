<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MarketingSeeder;
use Database\Seeders\RbacSeeder;
use Database\Seeders\SocialSeeder;
use Database\Seeders\SupportSeeder;
use Database\Seeders\WorkflowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AutomationModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_marketing_and_workflow_modules_render_real_data(): void
    {
        $this->seed([
            RbacSeeder::class,
            SocialSeeder::class,
            MarketingSeeder::class,
            WorkflowSeeder::class,
            SupportSeeder::class,
        ]);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();
        $buyer = User::where('email', 'buyer@plexus.test')->firstOrFail();

        $this->actingAs($marketing)
            ->get('/social')
            ->assertRedirect('/social/calendar');

        $this->actingAs($marketing)
            ->get('/social/calendar')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Calendar')
                ->has('posts', 2)
                ->where('statuses.0', 'draft'));

        $this->actingAs($marketing)
            ->get('/social/posts')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Posts/Index')
                ->where('workspace.title', 'Social Posts')
                ->has('workspace.rows', 2));

        $this->actingAs($marketing)
            ->get('/social/accounts')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Accounts/Index')
                ->where('workspace.title', 'Social Accounts')
                ->has('workspace.rows', 2));

        $this->actingAs($marketing)
            ->get('/marketing')
            ->assertRedirect('/marketing/campaigns');

        $this->actingAs($marketing)
            ->get('/marketing/campaigns')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Campaigns/Index')
                ->where('workspace.title', 'Campaigns')
                ->has('workspace.rows', 1));

        $this->actingAs($marketing)
            ->get('/marketing/templates')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Templates/Index')
                ->where('workspace.title', 'Campaign Templates')
                ->has('workspace.rows', 5));

        $this->actingAs($marketing)
            ->get('/workflow')
            ->assertRedirect('/workflow/rules');

        $this->actingAs($marketing)
            ->get('/workflow/rules')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Workflow/Rules/Index')
                ->where('workspace.title', 'Automation Rules')
                ->has('workspace.rows', 6));

        $this->actingAs($marketing)
            ->get('/workflow/logs')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Workflow/Logs/Index')
                ->where('workspace.title', 'Workflow Logs'));

        $this->actingAs($buyer)
            ->get('/support')
            ->assertRedirect('/support/tickets');

        $this->actingAs($buyer)
            ->get('/support/tickets')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Support/Tickets/Index')
                ->where('workspace.title', 'Support Tickets')
                ->has('workspace.rows', 2));

        $this->actingAs($buyer)
            ->get('/support/faq')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Support/Faq/Index')
                ->where('workspace.title', 'Support FAQ')
                ->has('workspace.rows', 2));
    }
}

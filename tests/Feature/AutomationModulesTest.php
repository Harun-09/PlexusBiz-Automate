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
        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
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
                ->where('workspace.title', 'Social Campaigns')
                ->has('workspace.rows', 2));

        $this->actingAs($marketing)
            ->get('/social/posts/scheduled')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Posts/Index')
                ->where('workspace.title', 'Scheduled Posts')
                ->has('workspace.rows', 2));

        $this->actingAs($marketing)
            ->get('/social/posts/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('Social/Posts/Create'));

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
                ->where('workspace.title', 'Email Campaigns')
                ->has('workspace.rows', 1));

        $this->actingAs($marketing)
            ->get('/marketing/templates')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Marketing/Templates/Index')
                ->where('workspace.title', 'Email Campaign Templates')
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
                ->has('workspace.rows', 7));

        $this->actingAs($marketing)
            ->get('/workflow/rules/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Workflow/Rules/Create')
                ->where('actions.0', 'send_email')
                ->where('actions.1', 'send_sms')
                ->where('actions.2', 'create_notification')
                ->where('actions.3', 'notify_supplier'));

        $this->actingAs($marketing)
            ->get('/workflow/logs')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Workflow/Logs/Index')
                ->where('workspace.title', 'Workflow Logs')
                ->where('workspace.columns', ['Rule', 'Trigger', 'Status', 'Executed', 'Payload', 'Result', 'Error']));

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

        $this->actingAs($marketing)
            ->get('/crm/leads/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page->component('CRM/Leads/Create'));

        $this->actingAs($admin)->get('/admin/leads/create')->assertRedirect('/crm/leads/create');
        $this->actingAs($admin)->get('/admin/social-posts/create')->assertRedirect('/social/posts/create');
        $this->actingAs($admin)->get('/admin/automation-rules/create')->assertRedirect('/workflow/rules/create');
        $this->actingAs($admin)->get('/admin/modules')->assertOk();
    }
}

<?php

namespace Tests\Feature\Social;

use App\Domains\Social\Models\SocialAccount;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Database\Seeders\SocialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SocialAccountCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_manager_can_create_edit_and_delete_social_accounts(): void
    {
        $this->seed([
            RbacSeeder::class,
            SocialSeeder::class,
        ]);

        $marketing = User::where('email', 'marketing@plexus.test')->firstOrFail();

        $this->actingAs($marketing)
            ->get('/social/accounts')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Accounts/Index')
                ->where('workspace.title', 'Social Accounts')
                ->has('workspace.rows', 2));

        $this->actingAs($marketing)
            ->get('/social/accounts/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Accounts/Create')
                ->where('platforms.0', 'facebook')
                ->where('statuses.0', 'active'));

        $this->actingAs($marketing)
            ->post('/social/accounts', [
                'platform' => 'facebook',
                'name' => 'PlexusBiz Instagram Intl',
                'handle' => '@plexusbiz-intl',
                'status' => 'inactive',
                'mode' => 'mock',
                'page_id' => '998877665544332',
                'access_token' => 'unit-test-facebook-token',
            ])
            ->assertRedirect('/social/accounts');

        $account = SocialAccount::where('handle', '@plexusbiz-intl')->firstOrFail();

        $this->assertSame('PlexusBiz Instagram Intl', $account->name);
        $this->assertSame('mock', $account->credentials_json['mode']);
        $this->assertSame('998877665544332', $account->credentials_json['page_id']);
        $this->assertSame('unit-test-facebook-token', $account->credentials_json['access_token']);

        $this->actingAs($marketing)
            ->get("/social/accounts/{$account->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Social/Accounts/Edit')
                ->where('account.id', $account->id)
                ->where('account.name', 'PlexusBiz Instagram Intl'));

        $this->actingAs($marketing)
            ->put("/social/accounts/{$account->id}", [
                'platform' => 'instagram',
                'name' => 'PlexusBiz Instagram Global',
                'handle' => '@plexusbiz-global',
                'status' => 'active',
                'mode' => 'live',
                'page_id' => '998877665544333',
                'access_token' => 'unit-test-facebook-token-2',
            ])
            ->assertRedirect('/social/accounts');

        $account->refresh();

        $this->assertSame('instagram', $account->platform->value);
        $this->assertSame('PlexusBiz Instagram Global', $account->name);
        $this->assertSame('@plexusbiz-global', $account->handle);
        $this->assertSame('active', $account->status);
        $this->assertSame('live', $account->credentials_json['mode']);
        $this->assertSame('998877665544333', $account->credentials_json['page_id']);
        $this->assertSame('unit-test-facebook-token-2', $account->credentials_json['access_token']);

        $this->actingAs($marketing)
            ->delete("/social/accounts/{$account->id}")
            ->assertRedirect('/social/accounts');

        $this->assertSoftDeleted('social_accounts', [
            'id' => $account->id,
        ]);
    }
}

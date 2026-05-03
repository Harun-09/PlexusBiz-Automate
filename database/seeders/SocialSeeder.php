<?php

namespace Database\Seeders;

use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use Illuminate\Database\Seeder;

class SocialSeeder extends Seeder
{
    public function run(): void
    {
        $facebook = SocialAccount::updateOrCreate(
            ['platform' => SocialPlatform::Facebook->value, 'handle' => '@plexusbiz'],
            [
                'name' => 'PlexusBiz Facebook',
                'status' => 'active',
                'credentials_json' => ['mode' => 'mock'],
            ],
        );

        $instagram = SocialAccount::updateOrCreate(
            ['platform' => SocialPlatform::Instagram->value, 'handle' => '@plexusbiz'],
            [
                'name' => 'PlexusBiz Instagram',
                'status' => 'active',
                'credentials_json' => ['mode' => 'mock'],
            ],
        );

        SocialPost::updateOrCreate(
            ['platform' => SocialPlatform::Facebook->value, 'content' => 'Priority B2B supply workflows are live.'],
            [
                'social_account_id' => $facebook->id,
                'scheduled_at' => now()->addDay(),
                'status' => SocialPostStatus::Scheduled,
            ],
        );

        SocialPost::updateOrCreate(
            ['platform' => SocialPlatform::Instagram->value, 'content' => 'Automated supplier operations for modern teams.'],
            [
                'social_account_id' => $instagram->id,
                'scheduled_at' => now()->addDays(2),
                'status' => SocialPostStatus::Scheduled,
            ],
        );
    }
}

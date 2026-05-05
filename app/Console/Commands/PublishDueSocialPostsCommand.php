<?php

namespace App\Console\Commands;

use App\Domains\Social\Services\SocialScheduleService;
use Illuminate\Console\Command;

class PublishDueSocialPostsCommand extends Command
{
    protected $signature = 'social-posts:publish-due';

    protected $description = 'Publish due scheduled social posts through the configured social adapters.';

    public function handle(SocialScheduleService $scheduler): int
    {
        $count = $scheduler->dispatchDuePosts(queued: false);

        $this->info("Published {$count} due social post".($count === 1 ? '' : 's').'.');

        return self::SUCCESS;
    }
}

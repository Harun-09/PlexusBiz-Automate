<?php

namespace App\Console\Commands;

use App\Domains\Social\Jobs\ProcessDueSocialPostsJob;
use Illuminate\Console\Command;

class PublishDueSocialPostsCommand extends Command
{
    protected $signature = 'social-posts:publish-due';

    protected $description = 'Publish due scheduled social posts through the configured mock adapters.';

    public function handle(): int
    {
        ProcessDueSocialPostsJob::dispatch();

        $this->info('Due social post processing completed.');

        return self::SUCCESS;
    }
}

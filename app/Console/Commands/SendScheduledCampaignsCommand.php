<?php

namespace App\Console\Commands;

use App\Domains\Marketing\Jobs\ProcessScheduledCampaignsJob;
use Illuminate\Console\Command;

class SendScheduledCampaignsCommand extends Command
{
    protected $signature = 'campaigns:send-scheduled';

    protected $description = 'Send marketing campaigns whose scheduled time has passed.';

    public function handle(): int
    {
        app()->call([new ProcessScheduledCampaignsJob(), 'handle']);

        $this->info('Scheduled campaign processing completed.');

        return self::SUCCESS;
    }
}

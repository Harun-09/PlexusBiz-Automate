<?php

namespace App\Console\Commands;

use App\Domains\Marketing\Jobs\ProcessAbandonedCartRemindersJob;
use Illuminate\Console\Command;

class CheckAbandonedCartsCommand extends Command
{
    protected $signature = 'carts:check-abandoned';

    protected $description = 'Mark old carts as abandoned and create reminder logs.';

    public function handle(): int
    {
        ProcessAbandonedCartRemindersJob::dispatch();

        $this->info('Abandoned cart reminder processing completed.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Domains\Marketing\Jobs;

use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Services\CampaignDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessScheduledCampaignsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CampaignDispatchService $dispatch): void
    {
        Campaign::query()
            ->where('status', CampaignStatus::Scheduled->value)
            ->where(function ($query): void {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->each(fn (Campaign $campaign): null => $dispatch->dispatch($campaign) ?: null);
    }
}

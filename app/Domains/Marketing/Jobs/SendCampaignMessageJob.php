<?php

namespace App\Domains\Marketing\Jobs;

use App\Domains\Marketing\Models\CampaignRecipient;
use App\Domains\Marketing\Services\CampaignDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaignMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly int $recipientId)
    {
    }

    public function handle(CampaignDispatchService $dispatch): void
    {
        $recipient = CampaignRecipient::findOrFail($this->recipientId);

        $dispatch->sendRecipient($recipient);
    }
}

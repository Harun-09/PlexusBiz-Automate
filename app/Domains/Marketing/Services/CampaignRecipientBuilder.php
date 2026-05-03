<?php

namespace App\Domains\Marketing\Services;

use App\Domains\CRM\Services\CustomerSegmentationService;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignRecipient;

class CampaignRecipientBuilder
{
    public function __construct(private readonly CustomerSegmentationService $segments)
    {
    }

    public function build(Campaign $campaign): int
    {
        $customers = $this->segments->query($campaign->segment_filters_json ?? [])->get();
        $created = 0;

        foreach ($customers as $customer) {
            $recipient = CampaignRecipient::firstOrCreate(
                ['campaign_id' => $campaign->id, 'customer_id' => $customer->id],
                [
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
            );

            if ($recipient->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }
}

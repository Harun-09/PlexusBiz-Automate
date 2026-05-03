<?php

namespace App\Domains\Social\Services;

use App\Domains\Social\Models\SocialPost;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class ContentCalendarService
{
    /**
     * @return Collection<int, SocialPost>
     */
    public function between(CarbonInterface $start, CarbonInterface $end): Collection
    {
        return SocialPost::query()
            ->whereBetween('scheduled_at', [$start, $end])
            ->orderBy('scheduled_at')
            ->get();
    }
}

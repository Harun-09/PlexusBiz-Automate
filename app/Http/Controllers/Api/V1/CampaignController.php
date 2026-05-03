<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Marketing\Models\Campaign;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\CampaignResource;

class CampaignController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', Campaign::class);

        $query = Campaign::query()->withCount(['recipients', 'logs']);

        $this->applySearch($query, $request, ['name', 'slug']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'scheduled_at', 'name']);

        return CampaignResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(Campaign $campaign): CampaignResource
    {
        $this->authorize('view', $campaign);

        return CampaignResource::make($campaign->loadCount(['recipients', 'logs']));
    }
}

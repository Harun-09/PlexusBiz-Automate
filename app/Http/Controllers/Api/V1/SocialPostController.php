<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Social\Models\SocialPost;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\SocialPostResource;

class SocialPostController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', SocialPost::class);

        $query = SocialPost::query()->with('campaign');

        $this->applySearch($query, $request, ['content']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'scheduled_at', 'published_at']);

        return SocialPostResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(SocialPost $socialPost): SocialPostResource
    {
        $this->authorize('view', $socialPost);

        return SocialPostResource::make($socialPost->load('campaign'));
    }
}

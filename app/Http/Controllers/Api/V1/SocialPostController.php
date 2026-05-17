<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Social\Services\SocialPlanningService;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\SocialPostResource;
use App\Support\DateTimeInput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialPostController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function __construct(private readonly SocialPlanningService $planner)
    {
    }

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SocialPost::class);

        $query = SocialPost::query()->with(['campaign', 'socialCampaign', 'contentCalendar']);

        $this->applySearch($query, $request, ['content']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'scheduled_at', 'published_at']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: SocialPostResource::class,
            message: 'Social posts fetched successfully.',
        );
    }

    public function show(SocialPost $socialPost): JsonResponse
    {
        $this->authorize('view', $socialPost);

        return $this->resourceResponse(
            SocialPostResource::make($socialPost->load(['campaign', 'socialCampaign', 'contentCalendar'])),
            'Social post details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', SocialPost::class);

        $validated = $this->validatePostData($request);
        $socialPost = SocialPost::create($this->postPayload($validated));
        $this->planner->syncPostArtifacts($socialPost->refresh(), $request->user()->id);

        return $this->resourceResponse(
            SocialPostResource::make($socialPost->fresh()->load(['campaign', 'socialCampaign', 'contentCalendar'])),
            'Social post created successfully',
            201,
        );
    }

    public function update(Request $request, SocialPost $socialPost): JsonResponse
    {
        $this->authorize('update', $socialPost);

        $validated = $this->validatePostData($request, $socialPost);

        if ($validated === []) {
            return $this->resourceResponse(
                SocialPostResource::make($socialPost->load(['campaign', 'socialCampaign', 'contentCalendar'])),
                'No changes submitted',
            );
        }

        $socialPost->forceFill($this->postPayload($validated, $socialPost))->save();
        $this->planner->syncPostArtifacts($socialPost->refresh(), $request->user()->id);

        return $this->resourceResponse(
            SocialPostResource::make($socialPost->refresh()->load(['campaign', 'socialCampaign', 'contentCalendar'])),
            'Social post updated successfully',
        );
    }

    public function destroy(SocialPost $socialPost): JsonResponse
    {
        $this->authorize('delete', $socialPost);

        $socialPost->delete();

        return $this->successResponse(
            data: null,
            message: 'Social post deleted successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePostData(Request $request, ?SocialPost $socialPost = null): array
    {
        $required = $socialPost === null ? 'required' : 'sometimes';

        return $request->validate([
            'campaign_id' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:campaigns,id'],
            'social_account_id' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:social_accounts,id'],
            'platform' => [$required, 'string', Rule::in($this->platforms())],
            'content' => [$required, 'string', 'max:5000'],
            'media_url' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:2048'],
            'scheduled_at' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
            'status' => [$required, 'string', Rule::in($this->statuses())],
            'external_post_id' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:255'],
            'published_at' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
            'failure_reason' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:5000'],
            'likes_count' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
            'comments_count' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
            'shares_count' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
            'reach_count' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
            'clicks_count' => [$socialPost === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function postPayload(array $validated, ?SocialPost $socialPost = null): array
    {
        $payload = [];

        foreach (['campaign_id', 'social_account_id', 'media_url', 'external_post_id', 'failure_reason'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field] === '' ? null : $validated[$field];
            }
        }

        if (array_key_exists('platform', $validated)) {
            $payload['platform'] = SocialPlatform::from($validated['platform']);
        }

        if (array_key_exists('content', $validated)) {
            $payload['content'] = trim((string) $validated['content']);
        }

        if (array_key_exists('scheduled_at', $validated)) {
            $payload['scheduled_at'] = DateTimeInput::toDatabase($validated['scheduled_at']);
        }

        if (array_key_exists('status', $validated)) {
            $payload['status'] = SocialPostStatus::from($validated['status']);
        } elseif ($socialPost === null) {
            $payload['status'] = SocialPostStatus::Draft;
        }

        if (array_key_exists('published_at', $validated)) {
            $payload['published_at'] = DateTimeInput::toDatabase($validated['published_at']);
        }

        foreach (['likes_count', 'comments_count', 'shares_count', 'reach_count', 'clicks_count'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field] ?? 0;
            }
        }

        if ($socialPost === null) {
            $payload['likes_count'] = $payload['likes_count'] ?? 0;
            $payload['comments_count'] = $payload['comments_count'] ?? 0;
            $payload['shares_count'] = $payload['shares_count'] ?? 0;
            $payload['reach_count'] = $payload['reach_count'] ?? 0;
            $payload['clicks_count'] = $payload['clicks_count'] ?? 0;
        }

        return $payload;
    }

    /**
     * @return array<int, string>
     */
    private function platforms(): array
    {
        return array_map(fn (SocialPlatform $platform): string => $platform->value, SocialPlatform::cases());
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return array_map(fn (SocialPostStatus $status): string => $status->value, SocialPostStatus::cases());
    }
}

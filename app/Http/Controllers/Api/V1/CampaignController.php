<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Models\Campaign;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\CampaignResource;
use App\Support\DateTimeInput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Campaign::class);

        $query = Campaign::query()->with('creator')->withCount(['recipients', 'logs', 'templates']);

        $this->applySearch($query, $request, ['name', 'slug']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'scheduled_at', 'name']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: CampaignResource::class,
            message: 'Campaigns fetched successfully.',
        );
    }

    public function show(Campaign $campaign): JsonResponse
    {
        $this->authorize('view', $campaign);

        return $this->resourceResponse(
            CampaignResource::make($campaign->load('creator')->loadCount(['recipients', 'logs', 'templates'])),
            'Campaign details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Campaign::class);

        $validated = $this->validateCampaign($request);

        $campaign = Campaign::create([
            'created_by' => $request->user()->id,
            'name' => trim($validated['name']),
            'slug' => $this->uniqueSlug(trim($validated['name'])),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'segment_filters_json' => $this->normalizeSegmentFilters($validated['segment_tags'] ?? null),
            'scheduled_at' => $this->parseDateTime($validated['scheduled_at'] ?? null),
        ]);

        return $this->resourceResponse(
            CampaignResource::make($campaign->load('creator')->loadCount(['recipients', 'logs', 'templates'])),
            'Campaign created successfully',
            201,
        );
    }

    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        $this->authorize('update', $campaign);

        $validated = $this->validateCampaign($request, $campaign);

        $campaign->forceFill([
            'name' => trim($validated['name']),
            'slug' => $this->uniqueSlug(trim($validated['name']), $campaign),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'segment_filters_json' => $this->normalizeSegmentFilters($validated['segment_tags'] ?? null),
            'scheduled_at' => $this->parseDateTime($validated['scheduled_at'] ?? null),
        ])->save();

        return $this->resourceResponse(
            CampaignResource::make($campaign->refresh()->load('creator')->loadCount(['recipients', 'logs', 'templates'])),
            'Campaign updated successfully',
        );
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return $this->successResponse(
            data: null,
            message: 'Campaign deleted successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCampaign(Request $request, ?Campaign $campaign = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in($this->campaignTypes())],
            'status' => ['required', 'string', Rule::in($this->campaignStatuses())],
            'segment_tags' => ['nullable', 'string', 'max:1000'],
            'scheduled_at' => ['nullable', 'date'],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function campaignTypes(): array
    {
        return [CampaignType::Email->value];
    }

    /**
     * @return array<int, string>
     */
    private function campaignStatuses(): array
    {
        return array_map(fn (CampaignStatus $status): string => $status->value, CampaignStatus::cases());
    }

    private function uniqueSlug(string $name, ?Campaign $ignoreCampaign = null): string
    {
        $base = Str::slug($name) ?: 'campaign';
        $slug = $base;
        $suffix = 2;

        while (Campaign::query()
            ->withTrashed()
            ->when($ignoreCampaign, fn ($query) => $query->whereKeyNot($ignoreCampaign->id))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param  mixed  $value
     * @return array<string, array<int, string>>|null
     */
    private function normalizeSegmentFilters(mixed $value): ?array
    {
        if (is_array($value)) {
            if (isset($value['tags']) && is_array($value['tags'])) {
                $tags = array_values(array_filter(array_map(fn ($tag) => trim((string) $tag), $value['tags'])));

                return $tags === [] ? null : ['tags' => $tags];
            }

            $tags = array_values(array_filter(array_map(fn ($tag) => trim((string) $tag), $value)));

            return $tags === [] ? null : ['tags' => $tags];
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return null;
        }

        $tags = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $raw) ?: [])));

        return $tags === [] ? null : ['tags' => $tags];
    }

    private function parseDateTime(mixed $value): ?string
    {
        return DateTimeInput::toDatabase($value);
    }
}

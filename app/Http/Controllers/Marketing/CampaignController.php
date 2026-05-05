<?php

namespace App\Http\Controllers\Marketing;

use App\Domains\Marketing\Enums\CampaignStatus;
use App\Domains\Marketing\Enums\CampaignType;
use App\Domains\Marketing\Models\Campaign;
use App\Http\Controllers\Controller;
use App\Support\DateTimeInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function create(): Response
    {
        $this->authorize('create', Campaign::class);

        return Inertia::render('Marketing/Campaigns/Create', [
            'campaignTypes' => $this->campaignTypes(),
            'statuses' => $this->campaignStatuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Campaign::class);

        $validated = $this->validateCampaign($request);

        Campaign::create([
            'created_by' => $request->user()->id,
            'name' => trim($validated['name']),
            'slug' => $this->uniqueSlug(trim($validated['name'])),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'segment_filters_json' => $this->normalizeSegmentTags($validated['segment_tags'] ?? null),
            'scheduled_at' => $this->parseDateTime($validated['scheduled_at'] ?? null),
        ]);

        return redirect()
            ->route('marketing.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function edit(Campaign $campaign): Response
    {
        $this->authorize('update', $campaign);

        $segmentFilters = $campaign->segment_filters_json ?? [];

        return Inertia::render('Marketing/Campaigns/Edit', [
            'campaign' => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'slug' => $campaign->slug,
                'type' => $campaign->type->value,
                'status' => $campaign->status->value,
                'segment_tags' => implode(', ', $segmentFilters['tags'] ?? []),
                'scheduled_at' => DateTimeInput::toInputValue($campaign->scheduled_at),
            ],
            'campaignTypes' => $this->campaignTypes(),
            'statuses' => $this->campaignStatuses(),
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        $validated = $this->validateCampaign($request, $campaign);

        $campaign->forceFill([
            'name' => trim($validated['name']),
            'slug' => $this->uniqueSlug(trim($validated['name']), $campaign),
            'type' => $validated['type'],
            'status' => $validated['status'],
            'segment_filters_json' => $this->normalizeSegmentTags($validated['segment_tags'] ?? null),
            'scheduled_at' => $this->parseDateTime($validated['scheduled_at'] ?? null),
        ])->save();

        return redirect()
            ->route('marketing.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return redirect()
            ->route('marketing.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
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
    private function normalizeSegmentTags(mixed $value): ?array
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

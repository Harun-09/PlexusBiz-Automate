<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\CampaignTemplateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CampaignTemplateController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', CampaignTemplate::class);

        $query = CampaignTemplate::query()->with('campaign');

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search', ''));

            $query->where(function ($builder) use ($search): void {
                $builder->where('template_key', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%')
                    ->orWhere('body', 'like', '%'.$search.'%')
                    ->orWhereHas('campaign', fn ($campaign) => $campaign->where('name', 'like', '%'.$search.'%'));
            });
        }

        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'name', 'template_key']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: CampaignTemplateResource::class,
            message: 'Campaign templates fetched successfully.',
        );
    }

    public function show(CampaignTemplate $campaignTemplate): JsonResponse
    {
        $this->authorize('view', $campaignTemplate);

        return $this->resourceResponse(
            CampaignTemplateResource::make($campaignTemplate->load('campaign')),
            'Campaign template details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', CampaignTemplate::class);

        $validated = $this->validateTemplate($request);

        $template = CampaignTemplate::create([
            'campaign_id' => $validated['campaign_id'] ?: null,
            'template_key' => $this->uniqueTemplateKey(trim((string) ($validated['template_key'] ?? '')), $validated['name']),
            'channel' => MessageChannel::Email->value,
            'name' => trim($validated['name']),
            'subject' => trim((string) ($validated['subject'] ?? '')) ?: null,
            'body' => $validated['body'],
            'variables' => $this->normalizeVariables($validated['variables'] ?? null),
            'status' => $validated['status'],
        ]);

        return $this->resourceResponse(
            CampaignTemplateResource::make($template->load('campaign')),
            'Template created successfully',
            201,
        );
    }

    public function update(Request $request, CampaignTemplate $campaignTemplate): JsonResponse
    {
        $this->authorize('update', $campaignTemplate);

        $validated = $this->validateTemplate($request, $campaignTemplate);

        $campaignTemplate->forceFill([
            'campaign_id' => $validated['campaign_id'] ?: null,
            'template_key' => $this->uniqueTemplateKey(trim((string) ($validated['template_key'] ?? '')), $validated['name'], $campaignTemplate),
            'channel' => MessageChannel::Email->value,
            'name' => trim($validated['name']),
            'subject' => trim((string) ($validated['subject'] ?? '')) ?: null,
            'body' => $validated['body'],
            'variables' => $this->normalizeVariables($validated['variables'] ?? null),
            'status' => $validated['status'],
        ])->save();

        return $this->resourceResponse(
            CampaignTemplateResource::make($campaignTemplate->refresh()->load('campaign')),
            'Template updated successfully',
        );
    }

    public function destroy(CampaignTemplate $campaignTemplate): JsonResponse
    {
        $this->authorize('delete', $campaignTemplate);

        $campaignTemplate->delete();

        return $this->successResponse(
            data: null,
            message: 'Template deleted successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTemplate(Request $request, ?CampaignTemplate $campaignTemplate = null): array
    {
        return $request->validate([
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
            'template_key' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'variables' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', Rule::in($this->templateStatuses())],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function templateStatuses(): array
    {
        return ['active', 'inactive'];
    }

    private function uniqueTemplateKey(string $templateKey, string $name, ?CampaignTemplate $ignoreTemplate = null): string
    {
        $base = $templateKey !== '' ? Str::slug($templateKey) : (Str::slug($name) ?: 'template');
        $key = $base;
        $suffix = 2;

        while (CampaignTemplate::query()
            ->withTrashed()
            ->when($ignoreTemplate, fn ($query) => $query->whereKeyNot($ignoreTemplate->id))
            ->where('template_key', $key)
            ->exists()) {
            $key = $base.'-'.$suffix;
            $suffix++;
        }

        return $key;
    }

    /**
     * @param  mixed  $value
     * @return array<int, string>|null
     */
    private function normalizeVariables(mixed $value): ?array
    {
        if (is_array($value)) {
            $variables = array_values(array_filter(array_map(fn ($variable) => trim((string) $variable), $value)));

            return $variables === [] ? null : $variables;
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return null;
        }

        $variables = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $raw) ?: [])));

        return $variables === [] ? null : $variables;
    }
}

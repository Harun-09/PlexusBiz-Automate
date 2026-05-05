<?php

namespace App\Http\Controllers\Marketing;

use App\Domains\Marketing\Enums\MessageChannel;
use App\Domains\Marketing\Models\Campaign;
use App\Domains\Marketing\Models\CampaignTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CampaignTemplateController extends Controller
{
    public function create(): Response
    {
        $this->authorize('create', CampaignTemplate::class);

        return Inertia::render('Marketing/Templates/Create', [
            'campaigns' => $this->campaignOptions(),
            'channels' => $this->channels(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CampaignTemplate::class);

        $validated = $this->validateTemplate($request);

        CampaignTemplate::create([
            'campaign_id' => ! empty($validated['campaign_id']) ? (int) $validated['campaign_id'] : null,
            'template_key' => $this->uniqueTemplateKey(trim((string) ($validated['template_key'] ?? '')), $validated['name']),
            'channel' => MessageChannel::Email->value,
            'name' => trim($validated['name']),
            'subject' => trim((string) ($validated['subject'] ?? '')) ?: null,
            'body' => $validated['body'],
            'variables' => $this->normalizeVariables($validated['variables'] ?? null),
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('marketing.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(CampaignTemplate $campaignTemplate): Response
    {
        $this->authorize('update', $campaignTemplate);

        return Inertia::render('Marketing/Templates/Edit', [
            'template' => [
                'id' => $campaignTemplate->id,
                'campaign_id' => $campaignTemplate->campaign_id,
                'template_key' => $campaignTemplate->template_key,
                'channel' => is_object($campaignTemplate->channel) ? $campaignTemplate->channel->value : (string) $campaignTemplate->channel,
                'name' => $campaignTemplate->name,
                'subject' => $campaignTemplate->subject ?? '',
                'body' => $campaignTemplate->body,
                'variables' => implode(', ', $campaignTemplate->variables ?? []),
                'status' => (string) $campaignTemplate->status,
            ],
            'campaigns' => $this->campaignOptions(),
            'channels' => $this->channels(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, CampaignTemplate $campaignTemplate): RedirectResponse
    {
        $this->authorize('update', $campaignTemplate);

        $validated = $this->validateTemplate($request, $campaignTemplate);

        $campaignTemplate->forceFill([
            'campaign_id' => ! empty($validated['campaign_id']) ? (int) $validated['campaign_id'] : null,
            'template_key' => $this->uniqueTemplateKey(trim((string) ($validated['template_key'] ?? '')), $validated['name'], $campaignTemplate),
            'channel' => MessageChannel::Email->value,
            'name' => trim($validated['name']),
            'subject' => trim((string) ($validated['subject'] ?? '')) ?: null,
            'body' => $validated['body'],
            'variables' => $this->normalizeVariables($validated['variables'] ?? null),
            'status' => $validated['status'],
        ])->save();

        return redirect()
            ->route('marketing.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(CampaignTemplate $campaignTemplate): RedirectResponse
    {
        $this->authorize('delete', $campaignTemplate);

        $campaignTemplate->delete();

        return redirect()
            ->route('marketing.templates.index')
            ->with('success', 'Template deleted successfully.');
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
            'status' => ['required', 'string', Rule::in($this->statuses())],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function channels(): array
    {
        return [MessageChannel::Email->value];
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return ['active', 'inactive'];
    }

    /**
     * @return array<int, array{id:int,label:string}>
     */
    private function campaignOptions(): array
    {
        return Campaign::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Campaign $campaign): array => [
                'id' => $campaign->id,
                'label' => $campaign->name,
            ])
            ->all();
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

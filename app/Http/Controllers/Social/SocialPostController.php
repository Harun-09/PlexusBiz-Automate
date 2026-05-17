<?php

namespace App\Http\Controllers\Social;

use App\Domains\Marketing\Models\Campaign;
use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Enums\SocialPostStatus;
use App\Domains\Social\Models\SocialAccount;
use App\Domains\Social\Models\SocialPost;
use App\Domains\Social\Services\SocialPlanningService;
use App\Http\Controllers\Controller;
use App\Support\DateTimeInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SocialPostController extends Controller
{
    public function __construct(private readonly SocialPlanningService $planner)
    {
    }

    public function create(): Response
    {
        $this->authorize('create', SocialPost::class);

        return Inertia::render('Social/Posts/Create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', SocialPost::class);

        $post = SocialPost::create($this->payload($this->validatePost($request)));
        $this->planner->syncPostArtifacts($post->refresh(), $request->user()->id);

        return redirect()
            ->route('social.posts.index')
            ->with('success', 'Social post scheduled successfully.');
    }

    public function edit(SocialPost $socialPost): Response
    {
        $this->authorize('update', $socialPost);

        return Inertia::render('Social/Posts/Edit', array_merge($this->formOptions(), [
            'post' => [
                'id' => $socialPost->id,
                'campaign_id' => $socialPost->campaign_id,
                'social_account_id' => $socialPost->social_account_id,
                'platform' => $socialPost->platform->value,
                'content' => $socialPost->content,
                'media_url' => $socialPost->media_url,
                'scheduled_at' => DateTimeInput::toInputValue($socialPost->scheduled_at),
                'status' => $socialPost->status->value,
                'likes_count' => $socialPost->likes_count,
                'comments_count' => $socialPost->comments_count,
                'shares_count' => $socialPost->shares_count,
            ],
        ]));
    }

    public function update(Request $request, SocialPost $socialPost): RedirectResponse
    {
        $this->authorize('update', $socialPost);

        $socialPost->forceFill($this->payload($this->validatePost($request)))->save();
        $this->planner->syncPostArtifacts($socialPost->refresh(), $request->user()->id);

        return redirect()
            ->route('social.posts.index')
            ->with('success', 'Social post updated successfully.');
    }

    public function destroy(SocialPost $socialPost): RedirectResponse
    {
        $this->authorize('delete', $socialPost);

        $socialPost->delete();

        return redirect()
            ->route('social.posts.index')
            ->with('success', 'Social post deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePost(Request $request): array
    {
        return $request->validate([
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
            'social_account_id' => ['nullable', 'integer', 'exists:social_accounts,id'],
            'platform' => ['required', 'string', Rule::in($this->platforms())],
            'content' => ['required', 'string', 'max:5000'],
            'media_url' => ['nullable', 'string', 'max:2048'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in($this->statuses())],
            'likes_count' => ['nullable', 'integer', 'min:0'],
            'comments_count' => ['nullable', 'integer', 'min:0'],
            'shares_count' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated): array
    {
        return [
            'campaign_id' => $validated['campaign_id'] ?? null,
            'social_account_id' => $validated['social_account_id'] ?? null,
            'platform' => SocialPlatform::from($validated['platform']),
            'content' => trim($validated['content']),
            'media_url' => $validated['media_url'] ?? null,
            'scheduled_at' => $this->parseDateTime($validated['scheduled_at'] ?? null),
            'status' => SocialPostStatus::from($validated['status']),
            'likes_count' => $validated['likes_count'] ?? 0,
            'comments_count' => $validated['comments_count'] ?? 0,
            'shares_count' => $validated['shares_count'] ?? 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'platforms' => $this->platforms(),
            'statuses' => $this->statuses(),
            'accounts' => SocialAccount::query()
                ->orderBy('platform')
                ->orderBy('name')
                ->get()
                ->map(function (SocialAccount $account): array {
                    $credentials = $account->credentials_json ?? [];
                    $pageId = is_array($credentials) ? (string) ($credentials['page_id'] ?? '') : '';
                    $parts = [ucfirst($account->platform->value), $account->name];

                    if (filled($account->handle)) {
                        $parts[] = $account->handle;
                    }

                    if (filled($pageId)) {
                        $parts[] = 'Page ID: '.$pageId;
                    }

                    return [
                        'id' => $account->id,
                        'label' => implode(' | ', $parts),
                        'platform' => $account->platform->value,
                        'page_id' => $pageId,
                    ];
                })
                ->all(),
            'campaigns' => Campaign::query()
                ->orderBy('name')
                ->limit(100)
                ->get()
                ->map(fn (Campaign $campaign): array => [
                    'id' => $campaign->id,
                    'label' => $campaign->name,
                ])
                ->all(),
        ];
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

    private function parseDateTime(mixed $value): ?string
    {
        return DateTimeInput::toDatabase($value);
    }
}

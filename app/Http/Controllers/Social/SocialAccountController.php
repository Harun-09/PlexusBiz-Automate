<?php

namespace App\Http\Controllers\Social;

use App\Domains\Social\Enums\SocialPlatform;
use App\Domains\Social\Models\SocialAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SocialAccountController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Social/Accounts/Create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        SocialAccount::create($this->payload($this->validateAccount($request)));

        return redirect()
            ->route('social.accounts.index')
            ->with('success', 'Social account created successfully.');
    }

    public function edit(SocialAccount $socialAccount): Response
    {
        return Inertia::render('Social/Accounts/Edit', array_merge($this->formOptions(), [
            'account' => $this->resource($socialAccount),
        ]));
    }

    public function update(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $socialAccount->forceFill($this->payload($this->validateAccount($request), $socialAccount))->save();

        return redirect()
            ->route('social.accounts.index')
            ->with('success', 'Social account updated successfully.');
    }

    public function destroy(SocialAccount $socialAccount): RedirectResponse
    {
        $socialAccount->delete();

        return redirect()
            ->route('social.accounts.index')
            ->with('success', 'Social account deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'platforms' => $this->platforms(),
            'statuses' => $this->statuses(),
            'modes' => $this->modes(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resource(SocialAccount $account): array
    {
        $credentials = $account->credentials_json ?? [];

        return [
            'id' => $account->id,
            'platform' => $account->platform->value,
            'name' => $account->name,
            'handle' => $account->handle,
            'status' => $account->status,
            'page_id' => is_array($credentials) ? (string) ($credentials['page_id'] ?? '') : '',
            'has_access_token' => is_array($credentials) && filled($credentials['access_token'] ?? null),
            'credentials_mode' => is_array($credentials) ? (string) ($credentials['mode'] ?? 'mock') : 'mock',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAccount(Request $request): array
    {
        return $request->validate([
            'platform' => ['required', 'string', Rule::in($this->platforms())],
            'name' => ['required', 'string', 'max:255'],
            'handle' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in($this->statuses())],
            'mode' => ['required', 'string', Rule::in($this->modes())],
            'page_id' => ['nullable', 'string', 'max:255'],
            'access_token' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated, ?SocialAccount $account = null): array
    {
        $existingCredentials = is_array($account?->credentials_json) ? $account->credentials_json : [];

        return [
            'platform' => SocialPlatform::from($validated['platform']),
            'name' => trim((string) $validated['name']),
            'handle' => filled($validated['handle'] ?? null) ? trim((string) $validated['handle']) : null,
            'status' => (string) $validated['status'],
            'credentials_json' => [
                'mode' => (string) $validated['mode'],
                'page_id' => filled($validated['page_id'] ?? null)
                    ? trim((string) $validated['page_id'])
                    : ($existingCredentials['page_id'] ?? null),
                'access_token' => filled($validated['access_token'] ?? null)
                    ? trim((string) $validated['access_token'])
                    : ($existingCredentials['access_token'] ?? null),
            ],
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
        return ['active', 'inactive'];
    }

    /**
     * @return array<int, string>
     */
    private function modes(): array
    {
        return ['mock', 'live'];
    }
}

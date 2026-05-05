<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Services\CustomerProfileService;
use App\Enums\RoleName;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly CustomerProfileService $profiles)
    {
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $customer = null;

        if ($request->user()?->hasRole(RoleName::Buyer->value) || $request->user()?->account_type === RoleName::Buyer->value) {
            $customer = $this->profiles->ensureForUser($request->user());
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'customer' => $customer ? [
                'id' => $customer->id,
                'company_name' => $customer->company_name,
                'contact_name' => $customer->contact_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'business_type' => $customer->business_type,
                'address' => $customer->address ?? [],
                'status' => $customer->status->value,
                'lifecycle_stage' => $customer->lifecycle_stage->value,
                'tags' => $customer->tags ?? [],
                'notes' => $customer->notes,
                'last_activity_at' => $customer->last_activity_at?->toIso8601String(),
                'created_at' => $customer->created_at?->toIso8601String(),
            ] : null,
            'customerSummary' => $customer ? $this->profiles->purchaseSummary($customer) : null,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->user()->hasRole(RoleName::Buyer->value) || $request->user()->account_type === RoleName::Buyer->value) {
            $customer = $this->profiles->ensureForUser($request->user());
            $customer->forceFill([
                'email' => $request->user()->email,
                'last_activity_at' => now(),
            ])->save();
        }

        return Redirect::route('profile.edit');
    }

    public function updateCustomer(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasRole(RoleName::Buyer->value) || $request->user()?->account_type === RoleName::Buyer->value, 403);

        $validated = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'business_type' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = $this->profiles->ensureForUser($request->user());
        $this->authorize('update', $customer);

        $address = array_filter([
            'line_1' => $validated['address_line1'] ?? null,
            'line_2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? null,
        ], fn (mixed $value): bool => ! blank($value));

        $customer->forceFill([
            'contact_name' => $validated['contact_name'],
            'company_name' => $validated['company_name'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'business_type' => $validated['business_type'] ?: null,
            'address' => $address !== [] ? $address : null,
            'email' => $request->user()->email,
            'last_activity_at' => now(),
        ])->save();

        return Redirect::route('profile.edit')->with('success', 'Customer profile updated.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

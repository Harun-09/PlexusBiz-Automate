<?php

namespace App\Http\Controllers;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Notifications\Services\MessageService;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class SupplierOnboardingController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Frontend/SupplierApply', [
            'countries' => [
                'Bangladesh',
                'India',
                'Singapore',
                'United Arab Emirates',
                'United States',
            ],
        ]);
    }

    public function store(Request $request, MessageService $messages): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
        ]);

        $user = DB::transaction(function () use ($validated, $messages): User {
            $address = array_filter([
                'line1' => $validated['address_line1'] ?? null,
                'line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
            ], fn ($value) => filled($value)) ?: null;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole(Role::findOrCreate(RoleName::Supplier->value));

            Supplier::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']).'-'.Str::lower(Str::random(4)),
                'status' => SupplierStatus::Pending->value,
                'contact_email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'tax_number' => $validated['tax_number'] ?? null,
                'address' => $address,
            ]);

            $this->notifyAdminsOfApplication($user, $validated['company_name'], $messages);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Supplier application submitted. Your profile is under review.');
    }

    private function notifyAdminsOfApplication(User $applicant, string $companyName, MessageService $messages): void
    {
        User::role(RoleName::Admin->value)
            ->get()
            ->each(function (User $admin) use ($applicant, $companyName, $messages): void {
                $messages->sendToUser(
                    receiver: $admin,
                    subject: "New supplier application: {$companyName}",
                    body: sprintf(
                        '%s (%s) submitted a supplier application for %s and it is waiting for review.',
                        $applicant->name,
                        $applicant->email,
                        $companyName,
                    ),
                );
            });
    }
}

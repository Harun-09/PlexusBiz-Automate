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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            'tin_number' => ['nullable', 'string', 'max:100'],
            'bin_number' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'verification_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'trade_license' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'corporate_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
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

            $supplier = Supplier::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']).'-'.Str::lower(Str::random(4)),
                'status' => SupplierStatus::Pending->value,
                'contact_email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'tax_number' => $validated['tax_number'] ?? null,
                'tin_number' => $validated['tin_number'] ?? null,
                'bin_number' => $validated['bin_number'] ?? null,
                'address' => $address,
            ]);

            if (! empty($validated['logo']) && $validated['logo'] instanceof UploadedFile) {
                $supplier->forceFill([
                    'logo_path' => $this->storeSupplierLogo($supplier, $validated['logo']),
                ])->save();
            }

            if (! empty($validated['verification_document']) && $validated['verification_document'] instanceof UploadedFile) {
                $supplier->forceFill([
                    'verification_document_path' => $this->storeSupplierDocument($supplier, $validated['verification_document'], 'verification'),
                    'verification_document_name' => $validated['verification_document']->getClientOriginalName(),
                ])->save();
            }

            if (! empty($validated['trade_license']) && $validated['trade_license'] instanceof UploadedFile) {
                $supplier->forceFill([
                    'trade_license_path' => $this->storeSupplierDocument($supplier, $validated['trade_license'], 'trade_license'),
                ])->save();
            }

            if (! empty($validated['corporate_certificate']) && $validated['corporate_certificate'] instanceof UploadedFile) {
                $supplier->forceFill([
                    'corporate_certificate_path' => $this->storeSupplierDocument($supplier, $validated['corporate_certificate'], 'corporate_certificate'),
                ])->save();
            }

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

    private function storeSupplierLogo(Supplier $supplier, UploadedFile $file): string
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $fileName = 'logo-'.Str::lower(Str::random(10)).($extension !== '' ? '.'.$extension : '');
        $directory = 'media/suppliers/'.$supplier->slug.'/logo';

        return Storage::disk('public')->putFileAs($directory, $file, $fileName);
    }

    private function storeSupplierDocument(Supplier $supplier, UploadedFile $file, string $prefix = 'verification'): string
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $fileName = $prefix.'-'.Str::lower(Str::random(10)).($extension !== '' ? '.'.$extension : '');
        $directory = 'media/suppliers/'.$supplier->slug.'/documents';

        return Storage::disk('public')->putFileAs($directory, $file, $fileName);
    }
}

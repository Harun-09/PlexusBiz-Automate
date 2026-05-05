<?php

namespace App\Http\Controllers\Admin;

use App\Domains\CRM\Services\CustomerProfileService;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\Notifications\Services\MessageService;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $role = (string) $request->query('role', '');

        $statuses = array_map(fn (UserStatus $s): string => $s->value, UserStatus::cases());
        $roles = RoleName::values();

        if ($status !== '' && ! in_array($status, $statuses, true)) {
            $status = '';
        }

        if ($role !== '' && ! in_array($role, $roles, true)) {
            $role = '';
        }

        $query = User::query()->with('roles');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($role !== '') {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $users = $query->latest()->paginate(20)->through(fn (User $user): array => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values()->all(),
            'status' => $user->status->value,
            'account_type' => $user->account_type,
            'created_at' => $user->created_at?->format('Y-m-d H:i'),
        ]);

        $pendingApplications = User::query()
            ->where('status', UserStatus::Pending->value)
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_type' => $user->account_type,
                'account_type_label' => $this->accountTypeLabel($user->account_type),
                'company_name' => $user->company_name,
                'job_title' => $user->job_title,
                'phone' => $user->phone,
                'employees' => $user->employees,
                'country' => $user->country,
                'created_at' => $user->created_at?->format('Y-m-d H:i'),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'pendingApplications' => $pendingApplications,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'role' => $role,
            ],
            'statuses' => $statuses,
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => RoleName::values(),
            'statuses' => array_map(fn (UserStatus $s): string => $s->value, UserStatus::cases()),
        ]);
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'string', Rule::in(RoleName::values())],
            'status' => ['required', 'string', Rule::in(array_map(fn (UserStatus $s): string => $s->value, UserStatus::cases()))],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);
        $user->load('roles');

        $auditLogger->record(
            actor: $request->user(),
            moduleKey: 'admin',
            action: 'admin.users.created',
            description: 'User account created from admin console.',
            subjectType: User::class,
            subjectId: $user->id,
            subjectLabel: $user->name,
            after: $this->userAuditSnapshot($user),
            metadata: [
                'role' => $validated['role'],
                'status' => $validated['status'],
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $user->load('roles');

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? '',
                'status' => $user->status->value,
            ],
            'roles' => RoleName::values(),
            'statuses' => array_map(fn (UserStatus $s): string => $s->value, UserStatus::cases()),
        ]);
    }

    public function update(Request $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'string', Rule::in(RoleName::values())],
            'status' => ['required', 'string', Rule::in(array_map(fn (UserStatus $s): string => $s->value, UserStatus::cases()))],
        ]);

        $before = $this->userAuditSnapshot($user->loadMissing('roles'));

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            ...(! empty($validated['password']) ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        $user->syncRoles([$validated['role']]);
        $user->refresh()->load('roles');
        $after = $this->userAuditSnapshot($user);

        if ($before !== $after || ! empty($validated['password'])) {
            $auditLogger->record(
                actor: $request->user(),
                moduleKey: 'admin',
                action: 'admin.users.updated',
                description: 'User account updated from admin console.',
                subjectType: User::class,
                subjectId: $user->id,
                subjectLabel: $user->name,
                before: $before,
                after: $after,
                metadata: [
                    'password_changed' => ! empty($validated['password']),
                ],
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
            );
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function approve(Request $request, User $user, AuditLogger $auditLogger, CustomerProfileService $customers, MessageService $messages): RedirectResponse
    {
        if ($user->status !== UserStatus::Pending) {
            return back()->with('error', 'Only pending applications can be approved.');
        }

        $accountType = RoleName::tryFrom((string) $user->account_type);

        if (! $accountType || $accountType === RoleName::Admin) {
            return back()->with('error', 'The application does not contain a valid account type.');
        }

        $before = $this->userAuditSnapshot($user->loadMissing('roles'));

        DB::transaction(function () use ($request, $user, $accountType, $customers): void {
            $user->forceFill([
                'status' => UserStatus::Active->value,
            ])->save();

            $user->syncRoles([$accountType->value]);

            match ($accountType) {
                RoleName::Buyer => $customers->ensureForUser($user, [
                    'contact_name' => $user->name,
                    'company_name' => $user->company_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'business_type' => $user->account_type,
                    'address' => null,
                    'tags' => ['approved-account'],
                ]),
                RoleName::Supplier => Supplier::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'approved_by' => $request->user()->id,
                        'company_name' => $user->company_name ?: $user->name,
                        'slug' => Str::slug($user->company_name ?: $user->name).'-'.Str::lower(Str::random(4)),
                        'status' => SupplierStatus::Approved->value,
                        'contact_email' => $user->email,
                        'phone' => $user->phone,
                        'tax_number' => null,
                        'address' => null,
                        'approved_at' => now(),
                    ],
                ),
                default => null,
            };
        });

        event(new Registered($user->fresh()));

        $messages->sendToUser(
            receiver: $user->fresh(),
            subject: 'Account application approved',
            body: sprintf(
                'Your %s application for %s has been approved. You can now sign in with your approved role.',
                $this->accountTypeLabel($user->account_type),
                $user->company_name ?: $user->name,
            ),
            sender: $request->user(),
        );

        $after = $this->userAuditSnapshot($user->fresh()->loadMissing('roles'));

        $auditLogger->record(
            actor: $request->user(),
            moduleKey: 'admin',
            action: 'admin.users.approved',
            description: 'User application approved from admin console.',
            subjectType: User::class,
            subjectId: $user->id,
            subjectLabel: $user->name,
            before: $before,
            after: $after,
            metadata: [
                'account_type' => $user->account_type,
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('admin.users.index')->with('success', 'Application approved successfully.');
    }

    public function reject(Request $request, User $user, AuditLogger $auditLogger, MessageService $messages): RedirectResponse
    {
        if ($user->status !== UserStatus::Pending) {
            return back()->with('error', 'Only pending applications can be rejected.');
        }

        $before = $this->userAuditSnapshot($user->loadMissing('roles'));

        $user->forceFill([
            'status' => UserStatus::Rejected->value,
        ])->save();

        $user->syncRoles([]);

        $messages->sendToUser(
            receiver: $user,
            subject: 'Account application rejected',
            body: sprintf(
                'Your %s application for %s was rejected. Please contact the admin team if you would like to discuss next steps.',
                $this->accountTypeLabel($user->account_type),
                $user->company_name ?: $user->name,
            ),
            sender: $request->user(),
        );

        $after = $this->userAuditSnapshot($user->fresh()->loadMissing('roles'));

        $auditLogger->record(
            actor: $request->user(),
            moduleKey: 'admin',
            action: 'admin.users.rejected',
            description: 'User application rejected from admin console.',
            subjectType: User::class,
            subjectId: $user->id,
            subjectLabel: $user->name,
            before: $before,
            after: $after,
            metadata: [
                'account_type' => $user->account_type,
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('admin.users.index')->with('success', 'Application rejected successfully.');
    }

    public function destroy(Request $request, User $user, AuditLogger $auditLogger): RedirectResponse
    {
        if ($user->id === request()->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $before = $this->userAuditSnapshot($user->loadMissing('roles'));
        $user->delete();

        $auditLogger->record(
            actor: $request->user(),
            moduleKey: 'admin',
            action: 'admin.users.deleted',
            description: 'User account deleted from admin console.',
            subjectType: User::class,
            subjectId: $user->id,
            subjectLabel: $user->name,
            before: $before,
            metadata: [
                'role' => $before['role'],
                'status' => $before['status'],
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function userAuditSnapshot(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status->value,
            'role' => $user->roles->first()?->name,
            'account_type' => $user->account_type,
            'company_name' => $user->company_name,
        ];
    }

    private function accountTypeLabel(?string $accountType): string
    {
        return RoleName::tryFrom((string) $accountType)?->label() ?? 'Account';
    }
}

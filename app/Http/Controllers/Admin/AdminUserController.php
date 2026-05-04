<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

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
            'created_at' => $user->created_at?->format('Y-m-d H:i'),
        ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
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
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminSupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $statuses = array_map(fn (SupplierStatus $s): string => $s->value, SupplierStatus::cases());

        if ($status !== '' && ! in_array($status, $statuses, true)) {
            $status = '';
        }

        $query = Supplier::query()->with('user');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $suppliers = $query->latest()->paginate(20)->through(fn (Supplier $supplier): array => [
            'id' => $supplier->id,
            'company_name' => $supplier->company_name,
            'owner' => $supplier->user?->name,
            'contact_email' => $supplier->contact_email,
            'phone' => $supplier->phone,
            'status' => $supplier->status->value,
            'approved_at' => $supplier->approved_at?->format('Y-m-d H:i'),
            'created_at' => $supplier->created_at?->format('Y-m-d H:i'),
        ]);

        return Inertia::render('Admin/Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'statuses' => $statuses,
        ]);
    }

    public function create(): Response
    {
        $availableUsers = User::query()
            ->whereDoesntHave('supplier')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $u): array => [
                'id' => $u->id,
                'label' => "{$u->name} ({$u->email})",
            ])
            ->all();

        return Inertia::render('Admin/Suppliers/Create', [
            'users' => $availableUsers,
            'statuses' => array_map(fn (SupplierStatus $s): string => $s->value, SupplierStatus::cases()),
        ]);
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('suppliers', 'user_id')],
            'company_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(array_map(fn (SupplierStatus $s): string => $s->value, SupplierStatus::cases()))],
        ]);

        $supplier = Supplier::create([
            ...$validated,
            'slug' => Str::slug($validated['company_name']),
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
            'approved_by' => $validated['status'] === 'approved' ? $request->user()->id : null,
        ]);

        // Assign supplier role to the user if not already assigned
        $user = User::find($validated['user_id']);
        if ($user && ! $user->hasRole('supplier')) {
            $user->assignRole('supplier');
        }

        $auditLogger->record(
            actor: $request->user(),
            moduleKey: 'admin',
            action: 'admin.suppliers.created',
            description: 'Supplier profile created from admin console.',
            subjectType: Supplier::class,
            subjectId: $supplier->id,
            subjectLabel: $supplier->company_name,
            after: $this->supplierAuditSnapshot($supplier->fresh()),
            metadata: [
                'user_id' => $supplier->user_id,
                'status' => $supplier->status->value,
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier): Response
    {
        $supplier->load('user');

        return Inertia::render('Admin/Suppliers/Edit', [
            'supplier' => [
                'id' => $supplier->id,
                'user_id' => $supplier->user_id,
                'user_label' => $supplier->user ? "{$supplier->user->name} ({$supplier->user->email})" : '',
                'company_name' => $supplier->company_name,
                'contact_email' => $supplier->contact_email,
                'phone' => $supplier->phone ?? '',
                'tax_number' => $supplier->tax_number ?? '',
                'status' => $supplier->status->value,
            ],
            'statuses' => array_map(fn (SupplierStatus $s): string => $s->value, SupplierStatus::cases()),
        ]);
    }

    public function update(Request $request, Supplier $supplier, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(array_map(fn (SupplierStatus $s): string => $s->value, SupplierStatus::cases()))],
        ]);

        $before = $this->supplierAuditSnapshot($supplier->loadMissing('user'));
        $wasApproved = $supplier->status === SupplierStatus::Approved;
        $isNowApproved = $validated['status'] === 'approved';

        $supplier->update([
            ...$validated,
            'slug' => Str::slug($validated['company_name']),
            ...($isNowApproved && ! $wasApproved ? [
                'approved_at' => now(),
                'approved_by' => $request->user()->id,
            ] : []),
        ]);

        $supplier->refresh()->loadMissing('user');
        $after = $this->supplierAuditSnapshot($supplier);

        if ($before !== $after) {
            $auditLogger->record(
                actor: $request->user(),
                moduleKey: 'admin',
                action: 'admin.suppliers.updated',
                description: 'Supplier profile updated from admin console.',
                subjectType: Supplier::class,
                subjectId: $supplier->id,
                subjectLabel: $supplier->company_name,
                before: $before,
                after: $after,
                metadata: [
                    'status_changed' => $before['status'] !== $after['status'],
                ],
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
            );
        }

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Request $request, Supplier $supplier, AuditLogger $auditLogger): RedirectResponse
    {
        $before = $this->supplierAuditSnapshot($supplier->loadMissing('user'));
        $supplier->delete();

        $auditLogger->record(
            actor: $request->user(),
            moduleKey: 'admin',
            action: 'admin.suppliers.deleted',
            description: 'Supplier profile deleted from admin console.',
            subjectType: Supplier::class,
            subjectId: $supplier->id,
            subjectLabel: $supplier->company_name,
            before: $before,
            metadata: [
                'status' => $before['status'],
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function supplierAuditSnapshot(Supplier $supplier): array
    {
        return [
            'user_id' => $supplier->user_id,
            'company_name' => $supplier->company_name,
            'contact_email' => $supplier->contact_email,
            'phone' => $supplier->phone,
            'tax_number' => $supplier->tax_number,
            'status' => $supplier->status->value,
            'approved_at' => $supplier->approved_at?->toDateTimeString(),
            'approved_by' => $supplier->approved_by,
        ];
    }
}

<?php

namespace App\Http\Controllers\Crm;

use App\Domains\CRM\Enums\LeadStatus;
use App\Domains\CRM\Models\Customer;
use App\Domains\CRM\Models\Lead;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\DateTimeInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('CRM/Leads/Create', [
            'statuses' => $this->statuses(),
            'customers' => $this->customerOptions(),
            'assignees' => $this->assigneeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLead($request);

        Lead::create($this->payload($validated));

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead): Response
    {
        return Inertia::render('CRM/Leads/Edit', [
            'lead' => [
                'id' => $lead->id,
                'customer_id' => $lead->customer_id,
                'assigned_user_id' => $lead->assigned_user_id,
                'source' => $lead->source,
                'status' => $lead->status->value,
                'company_name' => $lead->company_name,
                'contact_name' => $lead->contact_name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'value' => $lead->value,
                'notes' => $lead->notes,
                'next_follow_up_at' => DateTimeInput::toInputValue($lead->next_follow_up_at),
            ],
            'statuses' => $this->statuses(),
            'customers' => $this->customerOptions(),
            'assignees' => $this->assigneeOptions(),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $this->validateLead($request);

        $lead->forceFill($this->payload($validated))->save();

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLead(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in($this->statuses())],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated): array
    {
        $status = LeadStatus::from($validated['status']);

        return [
            'customer_id' => $validated['customer_id'] ?? null,
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
            'source' => $validated['source'] ?? null,
            'status' => $status,
            'company_name' => $validated['company_name'] ?? null,
            'contact_name' => trim($validated['contact_name']),
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'value' => $validated['value'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'next_follow_up_at' => $this->parseDateTime($validated['next_follow_up_at'] ?? null),
            'converted_at' => $status === LeadStatus::Converted ? now() : null,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return array_map(fn (LeadStatus $status): string => $status->value, LeadStatus::cases());
    }

    /**
     * @return array<int, array{id: int, label: string}>
     */
    private function customerOptions(): array
    {
        return Customer::buyerAccounts()
            ->orderBy('contact_name')
            ->limit(100)
            ->get()
            ->map(fn (Customer $customer): array => [
                'id' => $customer->id,
                'label' => trim($customer->contact_name.' - '.($customer->company_name ?: $customer->email)),
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, label: string}>
     */
    private function assigneeOptions(): array
    {
        return User::query()
            ->role(['admin', 'marketing_manager'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'label' => "{$user->name} ({$user->email})",
            ])
            ->all();
    }

    private function parseDateTime(mixed $value): ?string
    {
        return DateTimeInput::toDatabase($value);
    }
}

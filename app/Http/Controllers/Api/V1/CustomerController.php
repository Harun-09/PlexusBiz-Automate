<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\CRM\Enums\CustomerLifecycleStage;
use App\Domains\CRM\Enums\CustomerStatus;
use App\Domains\CRM\Models\Customer;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::buyerAccounts();

        $this->applySearch($query, $request, ['company_name', 'contact_name', 'email', 'phone']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'last_activity_at', 'company_name']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: CustomerResource::class,
            message: 'Customers fetched successfully.',
        );
    }

    public function show(Customer $customer): JsonResponse
    {
        $this->authorize('view', $customer);

        return $this->resourceResponse(
            CustomerResource::make($customer),
            'Customer details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Customer::class);

        $validated = $this->validateCustomer($request);
        $customer = Customer::create($this->customerPayload($validated));

        return $this->resourceResponse(
            CustomerResource::make($customer),
            'Customer created successfully',
            201,
        );
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('update', $customer);

        $validated = $this->validateCustomer($request, $customer);

        if ($validated === []) {
            return $this->resourceResponse(
                CustomerResource::make($customer),
                'No changes submitted',
            );
        }

        $customer->forceFill($this->customerPayload($validated, $customer))->save();

        return $this->resourceResponse(
            CustomerResource::make($customer->refresh()),
            'Customer updated successfully',
        );
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return $this->successResponse(
            data: null,
            message: 'Customer deleted successfully',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCustomer(Request $request, ?Customer $customer = null): array
    {
        $required = $customer === null ? 'required' : 'sometimes';

        return $request->validate([
            'user_id' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'integer', Rule::unique('customers', 'user_id')->ignore($customer?->id), 'exists:users,id'],
            'company_name' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:255'],
            'contact_name' => [$required, 'string', 'max:255'],
            'email' => [$required, 'string', 'email', 'max:255'],
            'phone' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:50'],
            'business_type' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:255'],
            'address' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'array'],
            'status' => [$required, 'string', Rule::in($this->customerStatuses())],
            'lifecycle_stage' => [$required, 'string', Rule::in($this->customerLifecycleStages())],
            'tags' => [$customer === null ? 'nullable' : 'sometimes'],
            'notes' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:5000'],
            'last_activity_at' => [$customer === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function customerPayload(array $validated, ?Customer $customer = null): array
    {
        $payload = [];

        foreach (['user_id', 'company_name', 'phone', 'business_type', 'address', 'notes', 'last_activity_at'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }

        if (array_key_exists('contact_name', $validated)) {
            $payload['contact_name'] = trim((string) $validated['contact_name']);
        }

        if (array_key_exists('email', $validated)) {
            $payload['email'] = strtolower(trim((string) $validated['email']));
        }

        if (array_key_exists('status', $validated)) {
            $payload['status'] = CustomerStatus::from($validated['status']);
        } elseif ($customer === null) {
            $payload['status'] = CustomerStatus::Active;
        }

        if (array_key_exists('lifecycle_stage', $validated)) {
            $payload['lifecycle_stage'] = CustomerLifecycleStage::from($validated['lifecycle_stage']);
        } elseif ($customer === null) {
            $payload['lifecycle_stage'] = CustomerLifecycleStage::Customer;
        }

        if (array_key_exists('tags', $validated)) {
            $payload['tags'] = $this->normalizeTags($validated['tags']);
        }

        return $payload;
    }

    /**
     * @param mixed $value
     * @return array<int, string>|null
     */
    private function normalizeTags(mixed $value): ?array
    {
        if (is_array($value)) {
            $tags = array_values(array_unique(array_filter(array_map(
                fn ($item): string => trim((string) $item),
                $value,
            ))));

            return $tags === [] ? null : $tags;
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return null;
        }

        $tags = array_values(array_unique(array_filter(array_map(
            'trim',
            preg_split('/[\r\n,]+/', $raw) ?: [],
        ))));

        return $tags === [] ? null : $tags;
    }

    /**
     * @return array<int, string>
     */
    private function customerStatuses(): array
    {
        return array_map(fn (CustomerStatus $status): string => $status->value, CustomerStatus::cases());
    }

    /**
     * @return array<int, string>
     */
    private function customerLifecycleStages(): array
    {
        return array_map(fn (CustomerLifecycleStage $stage): string => $stage->value, CustomerLifecycleStage::cases());
    }
}

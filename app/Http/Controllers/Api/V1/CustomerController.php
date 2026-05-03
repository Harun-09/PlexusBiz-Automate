<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\CRM\Models\Customer;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\CustomerResource;

class CustomerController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query();

        $this->applySearch($query, $request, ['company_name', 'contact_name', 'email', 'phone']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'last_activity_at', 'company_name']);

        return CustomerResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(Customer $customer): CustomerResource
    {
        $this->authorize('view', $customer);

        return CustomerResource::make($customer);
    }
}

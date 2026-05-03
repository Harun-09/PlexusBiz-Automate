<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\ProductResource;

class ProductController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with('supplier');

        if ($request->user()->hasRole('buyer')) {
            $query->where('status', ProductStatus::Active->value);
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $query->whereHas('supplier', fn ($supplier) => $supplier->where('user_id', $request->user()->id));
        }

        $this->applySearch($query, $request, ['sku', 'name', 'description']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'name', 'base_price']);

        return ProductResource::collection($query->paginate($request->perPage())->withQueryString());
    }

    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return ProductResource::make($product->load('supplier'));
    }
}

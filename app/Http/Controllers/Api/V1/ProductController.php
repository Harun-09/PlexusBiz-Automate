<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use AppliesApiFilters;

    public function index(ApiIndexRequest $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with(['supplier', 'images']);

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

        return ProductResource::make($product->load(['supplier', 'images']));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $validated = $this->validateProduct($request);
        $status = $validated['status'];
        $publishedAt = $validated['published_at'] ?? null;

        $product = Product::create([
            'supplier_id' => $this->resolveSupplierId($request, isset($validated['supplier_id']) ? (int) $validated['supplier_id'] : null),
            'category_id' => $validated['category_id'] ?? null,
            'sku' => trim($validated['sku']),
            'name' => trim($validated['name']),
            'slug' => $this->uniqueProductSlug(trim($validated['name'])),
            'description' => $validated['description'] ?? null,
            'base_price' => $validated['base_price'],
            'moq' => $validated['moq'],
            'stock_quantity' => $validated['stock_quantity'],
            'reserved_quantity' => $validated['reserved_quantity'] ?? 0,
            'status' => $status,
            'published_at' => $status === ProductStatus::Active->value
                ? ($publishedAt ?? now())
                : null,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => ProductResource::make($product->load(['supplier', 'images'])),
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $this->validateProduct($request, $product);

        if ($validated === []) {
            return response()->json([
                'message' => 'No changes submitted',
                'data' => ProductResource::make($product->load(['supplier', 'images'])),
            ]);
        }

        $payload = [];

        if (array_key_exists('supplier_id', $validated)) {
            $payload['supplier_id'] = $this->resolveSupplierId($request, $validated['supplier_id'] !== null ? (int) $validated['supplier_id'] : null);
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $payload['supplier_id'] = $this->resolveSupplierId($request);
        }

        if (array_key_exists('category_id', $validated)) {
            $payload['category_id'] = $validated['category_id'];
        }

        if (array_key_exists('sku', $validated)) {
            $payload['sku'] = trim($validated['sku']);
        }

        if (array_key_exists('name', $validated)) {
            $name = trim($validated['name']);
            $payload['name'] = $name;
            $payload['slug'] = $this->uniqueProductSlug($name, $product);
        }

        if (array_key_exists('description', $validated)) {
            $payload['description'] = $validated['description'] ?: null;
        }

        foreach (['base_price', 'moq', 'stock_quantity', 'reserved_quantity'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }

        if (array_key_exists('status', $validated)) {
            $status = $validated['status'];
            $payload['status'] = $status;
            $payload['published_at'] = $status === ProductStatus::Active->value
                ? ($validated['published_at'] ?? $product->published_at ?? now())
                : null;
        } elseif (array_key_exists('published_at', $validated)) {
            $payload['published_at'] = $validated['published_at'];
        }

        $product->forceFill($payload)->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => ProductResource::make($product->refresh()->load(['supplier', 'images'])),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $skuRules = ['string', 'max:100', Rule::unique('products', 'sku')];

        if ($product !== null) {
            $skuRules[2] = Rule::unique('products', 'sku')->ignore($product->id);
        }

        $required = $product === null ? 'required' : 'sometimes';

        return $request->validate([
            'supplier_id' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:suppliers,id'],
            'category_id' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'sku' => [$required, ...$skuRules],
            'name' => [$required, 'string', 'max:255'],
            'description' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'string', 'max:5000'],
            'base_price' => [$required, 'numeric', 'min:0'],
            'moq' => [$required, 'integer', 'min:1'],
            'stock_quantity' => [$required, 'integer', 'min:0'],
            'reserved_quantity' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
            'status' => [$required, 'string', Rule::in($this->productStatuses())],
            'published_at' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function productStatuses(): array
    {
        return array_map(fn (ProductStatus $status): string => $status->value, ProductStatus::cases());
    }

    private function uniqueProductSlug(string $name, ?Product $ignoreProduct = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::query()
            ->withTrashed()
            ->when($ignoreProduct, fn ($query) => $query->whereKeyNot($ignoreProduct->id))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function resolveSupplierId(Request $request, ?int $supplierId = null): int
    {
        if ($request->user()->hasRole('admin')) {
            if ($supplierId !== null && Supplier::query()->whereKey($supplierId)->exists()) {
                return $supplierId;
            }

            throw ValidationException::withMessages([
                'supplier_id' => 'A valid supplier_id is required.',
            ]);
        }

        $supplier = $request->user()->supplier;

        if (! $supplier?->isApproved()) {
            throw ValidationException::withMessages([
                'supplier_id' => 'An approved supplier profile is required.',
            ]);
        }

        return (int) $supplier->id;
    }
}

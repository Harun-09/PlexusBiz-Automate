<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ECommerce\ProductUpsertData;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Api\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\AppliesApiFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiIndexRequest;
use App\Http\Resources\Api\ProductResource;
use App\Repositories\ECommerce\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use AppliesApiFilters;
    use FormatsApiResponses;

    public function __construct(private readonly ProductRepositoryInterface $products)
    {
    }

    public function index(ApiIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $query = $this->products->query()->with(['supplier', 'images']);

        if ($request->user()->hasRole('buyer')) {
            $query->where('status', ProductStatus::Active->value);
        } elseif ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')) {
            $query->whereHas('supplier', fn ($supplier) => $supplier->where('user_id', $request->user()->id));
        }

        $this->applySearch($query, $request, ['sku', 'name', 'description']);
        $this->applyStatus($query, $request);
        $this->applySort($query, $request, ['created_at', 'updated_at', 'name', 'base_price']);

        $paginator = $query->paginate($request->perPage())->withQueryString();

        return $this->paginatedResourceResponse(
            paginator: $paginator,
            resourceClass: ProductResource::class,
            message: 'Products fetched successfully.',
        );
    }

    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return $this->resourceResponse(
            ProductResource::make($product->load(['supplier', 'images'])),
            'Product details fetched successfully.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $validated = $this->validateProduct($request);
        $supplierId = $this->resolveSupplierId($request, isset($validated['supplier_id']) ? (int) $validated['supplier_id'] : null);
        $product = $this->products->create(ProductUpsertData::fromValidated(
            validated: $validated,
            supplierId: $supplierId,
            normalizedTags: $this->normalizeTags($validated['tags'] ?? null),
            slug: $this->uniqueProductSlug(trim((string) $validated['name'])),
        ));

        return $this->resourceResponse(
            ProductResource::make($product->load(['supplier', 'images'])),
            'Product created successfully',
            201,
        );
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $this->validateProduct($request, $product);

        if ($validated === []) {
            return $this->resourceResponse(
                ProductResource::make($product->load(['supplier', 'images'])),
                'No changes submitted.',
            );
        }

        $supplierId = array_key_exists('supplier_id', $validated)
            ? $this->resolveSupplierId($request, $validated['supplier_id'] !== null ? (int) $validated['supplier_id'] : null)
            : ($request->user()->hasRole('supplier') && ! $request->user()->hasRole('admin')
                ? $this->resolveSupplierId($request)
                : (int) $product->supplier_id);

        $slug = array_key_exists('name', $validated)
            ? $this->uniqueProductSlug(trim((string) $validated['name']), $product)
            : $product->slug;

        $normalizedTags = array_key_exists('tags', $validated)
            ? $this->normalizeTags($validated['tags'])
            : (is_array($product->tags) ? $product->tags : null);

        $product = $this->products->update($product, ProductUpsertData::fromValidated(
            validated: $validated,
            supplierId: $supplierId,
            normalizedTags: $normalizedTags,
            slug: $slug,
            existingProduct: $product,
        ));

        return $this->resourceResponse(
            ProductResource::make($product->refresh()->load(['supplier', 'images'])),
            'Product updated successfully',
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $this->products->delete($product);

        return $this->successResponse(
            data: null,
            message: 'Product deleted successfully',
        );
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
            'tags' => [$product === null ? 'nullable' : 'sometimes', 'nullable'],
            'base_price' => [$required, 'numeric', 'min:0'],
            'moq' => [$required, 'integer', 'min:1'],
            'stock_quantity' => [$required, 'integer', 'min:0'],
            'reserved_quantity' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:0'],
            'status' => [$required, 'string', Rule::in($this->productStatuses())],
            'published_at' => [$product === null ? 'nullable' : 'sometimes', 'nullable', 'date'],
        ]);
    }

    /**
     * @param mixed $value
     * @return array<int, string>|null
     */
    private function normalizeTags(mixed $value): ?array
    {
        if (is_array($value)) {
            $tags = array_values(array_unique(array_filter(array_map(
                fn ($tag): string => trim((string) $tag),
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

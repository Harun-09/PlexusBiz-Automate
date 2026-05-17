<?php

namespace App\Http\Controllers\Admin;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Controller;
use App\Support\Media\AssetStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminProductController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $supplierId = (string) $request->query('supplier', '');

        $statuses = array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases());

        if ($status !== '' && ! in_array($status, $statuses, true)) {
            $status = '';
        }

        $query = Product::query()->with('supplier.user');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($supplierId !== '') {
            $query->where('supplier_id', $supplierId);
        }

        $products = $query->latest()->paginate(20)->through(fn (Product $product): array => [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'supplier' => $product->supplier?->company_name,
            'supplier_id' => $product->supplier_id,
            'tags' => is_array($product->tags) ? implode(', ', $product->tags) : '',
            'base_price' => $product->base_price,
            'stock' => $product->availableStock(),
            'moq' => $product->moq,
            'status' => $product->status->value,
            'created_at' => $product->created_at?->format('Y-m-d H:i'),
        ]);

        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $s): array => [
                'id' => $s->id,
                'label' => $s->company_name,
            ])
            ->all();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'supplier' => $supplierId,
            ],
            'statuses' => $statuses,
            'suppliers' => $suppliers,
        ]);
    }

    public function create(): Response
    {
        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $s): array => [
                'id' => $s->id,
                'label' => $s->company_name,
            ])
            ->all();

        return Inertia::render('Admin/Products/Create', [
            'suppliers' => $suppliers,
            'statuses' => array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()),
        ]);
    }

    public function store(Request $request, AssetStorageService $assets): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:1000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'moq' => ['required', 'integer', 'min:1'],
            'bulk_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()))],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ]);

        $bulkPrice = $validated['bulk_price'] ?? null;
        $image = $validated['image'] ?? null;
        unset($validated['bulk_price']);
        unset($validated['image']);

        $product = Product::create([
            ...$validated,
            'slug' => $this->uniqueProductSlug($validated['name']),
            'tags' => $this->normalizeTags($validated['tags'] ?? null),
            'reserved_quantity' => 0,
            'published_at' => $validated['status'] === 'active' ? now() : null,
        ]);

        $this->syncBulkTier($product, $bulkPrice);

        if ($image !== null) {
            $assets->replaceProductImage($product, $image, [
                'alt_text' => $product->name,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        $product->load(['supplier', 'pricingTiers', 'images']);

        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $s): array => [
                'id' => $s->id,
                'label' => $s->company_name,
            ])
            ->all();

        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'supplier_id' => $product->supplier_id,
                'sku' => $product->sku,
                'name' => $product->name,
                'description' => $product->description ?? '',
                'tags' => is_array($product->tags) ? implode(', ', $product->tags) : '',
                'base_price' => $product->base_price,
                'moq' => $product->moq,
                'bulk_price' => $product->pricingTiers->sortBy('min_quantity')->first()?->unit_price,
                'stock_quantity' => $product->stock_quantity,
                'status' => $product->status->value,
                'primary_image_url' => $product->primaryImage()?->url(),
            ],
            'suppliers' => $suppliers,
            'statuses' => array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()),
        ]);
    }

    public function update(Request $request, Product $product, AssetStorageService $assets): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:1000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'moq' => ['required', 'integer', 'min:1'],
            'bulk_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases()))],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ]);

        $bulkPrice = $validated['bulk_price'] ?? null;
        $image = $validated['image'] ?? null;
        unset($validated['bulk_price']);
        unset($validated['image']);

        $oldMoq = (int) $product->moq;
        $wasActive = $product->status === ProductStatus::Active;
        $isNowActive = $validated['status'] === 'active';

        $product->update([
            ...$validated,
            'slug' => $this->uniqueProductSlug($validated['name'], $product),
            'tags' => $this->normalizeTags($validated['tags'] ?? null),
            ...($isNowActive && ! $wasActive ? ['published_at' => now()] : []),
        ]);

        if ($oldMoq !== (int) $product->moq) {
            PricingTier::query()
                ->where('product_id', $product->id)
                ->where('min_quantity', $oldMoq)
                ->delete();
        }

        $this->syncBulkTier($product, $bulkPrice);

        if ($image !== null) {
            $assets->replaceProductImage($product, $image, [
                'alt_text' => $product->name,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    private function syncBulkTier(Product $product, mixed $bulkPrice): void
    {
        $raw = trim((string) $bulkPrice);

        if ($raw === '') {
            PricingTier::query()
                ->where('product_id', $product->id)
                ->where('min_quantity', (int) $product->moq)
                ->delete();

            return;
        }

        PricingTier::updateOrCreate(
            [
                'product_id' => $product->id,
                'min_quantity' => (int) $product->moq,
            ],
            [
                'unit_price' => $raw,
            ],
        );
    }

    private function uniqueProductSlug(string $name, ?Product $ignoreProduct = null): string
    {
        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Product::query()
                ->when($ignoreProduct !== null, fn ($query) => $query->where('id', '!=', $ignoreProduct->id))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param mixed $value
     * @return array<int, string>|null
     */
    private function normalizeTags(mixed $value): ?array
    {
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
}

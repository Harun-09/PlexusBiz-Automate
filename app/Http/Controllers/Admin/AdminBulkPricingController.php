<?php

namespace App\Http\Controllers\Admin;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminBulkPricingController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $supplierId = trim((string) $request->query('supplier', ''));

        $statuses = array_map(fn (ProductStatus $s): string => $s->value, ProductStatus::cases());

        if ($status !== '' && ! in_array($status, $statuses, true)) {
            $status = '';
        }

        $query = Product::query()
            ->with(['supplier', 'pricingTiers'])
            ->withCount('pricingTiers');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhereHas('supplier', fn ($supplier) => $supplier->where('company_name', 'like', '%'.$search.'%'));
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($supplierId !== '') {
            $query->where('supplier_id', $supplierId);
        }

        $productsPaginator = $query->latest()->paginate(12)->withQueryString();
        $selectedProductId = $request->integer('product') ?: $productsPaginator->getCollection()->first()?->id;
        $products = $productsPaginator->through(fn (Product $product): array => $this->presentListProduct($product));
        $selectedProduct = $selectedProductId
            ? Product::query()
                ->with(['supplier', 'pricingTiers' => fn ($tiers) => $tiers->orderBy('min_quantity')])
                ->find($selectedProductId)
            : null;

        $suppliers = Supplier::query()
            ->where('status', 'approved')
            ->orderBy('company_name')
            ->get(['id', 'company_name'])
            ->map(fn (Supplier $supplier): array => [
                'id' => $supplier->id,
                'label' => $supplier->company_name,
            ])
            ->values()
            ->all();

        return Inertia::render('Admin/BulkPricing/Index', [
            'summary' => [
                'total_products' => (int) Product::count(),
                'products_with_tiers' => (int) Product::has('pricingTiers')->count(),
                'products_without_tiers' => (int) Product::doesntHave('pricingTiers')->count(),
                'total_tiers' => (int) PricingTier::count(),
                'average_moq' => round((float) Product::avg('moq'), 1),
            ],
            'products' => $products,
            'selectedProduct' => $selectedProduct ? $this->presentSelectedProduct($selectedProduct) : null,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'supplier' => $supplierId,
                'product' => $selectedProductId ? (string) $selectedProductId : '',
            ],
            'statuses' => $statuses,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'moq' => ['required', 'integer', 'min:1'],
        ]);

        $product->update([
            'moq' => $validated['moq'],
        ]);

        return redirect()->back()->with('success', 'MOQ updated successfully.');
    }

    public function storeTier(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'min_quantity' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('pricing_tiers', 'min_quantity')->where(fn ($query) => $query->where('product_id', $product->id)),
            ],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->ensureTierQuantityIsValid($product, (int) $validated['min_quantity']);

        PricingTier::create([
            'product_id' => $product->id,
            'min_quantity' => (int) $validated['min_quantity'],
            'unit_price' => $validated['unit_price'],
        ]);

        return redirect()->back()->with('success', 'Pricing tier created successfully.');
    }

    public function updateTier(Request $request, Product $product, PricingTier $tier): RedirectResponse
    {
        $this->ensureTierBelongsToProduct($product, $tier);

        $validated = $request->validate([
            'min_quantity' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('pricing_tiers', 'min_quantity')
                    ->where(fn ($query) => $query->where('product_id', $product->id))
                    ->ignore($tier->id),
            ],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->ensureTierQuantityIsValid($product, (int) $validated['min_quantity']);

        $tier->update([
            'min_quantity' => (int) $validated['min_quantity'],
            'unit_price' => $validated['unit_price'],
        ]);

        return redirect()->back()->with('success', 'Pricing tier updated successfully.');
    }

    public function destroyTier(Product $product, PricingTier $tier): RedirectResponse
    {
        $this->ensureTierBelongsToProduct($product, $tier);

        $tier->delete();

        return redirect()->back()->with('success', 'Pricing tier deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function presentListProduct(Product $product): array
    {
        $product->loadMissing(['supplier', 'pricingTiers']);
        $tiers = $product->pricingTiers->sortBy('min_quantity')->values();

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'supplier' => $product->supplier?->company_name,
            'base_price' => (float) $product->base_price,
            'moq' => (int) $product->moq,
            'status' => $product->status->value,
            'pricing_tiers_count' => (int) $tiers->count(),
            'lowest_tier_price' => $tiers->first()?->unit_price !== null ? (float) $tiers->first()->unit_price : null,
            'highest_tier_price' => $tiers->last()?->unit_price !== null ? (float) $tiers->last()->unit_price : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentSelectedProduct(Product $product): array
    {
        $product->loadMissing(['supplier', 'pricingTiers']);
        $tiers = $product->pricingTiers->sortBy('min_quantity')->values();

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'status' => $product->status->value,
            'base_price' => (float) $product->base_price,
            'moq' => (int) $product->moq,
            'stock_quantity' => (int) $product->stock_quantity,
            'available_stock' => (int) $product->availableStock(),
            'supplier' => [
                'id' => $product->supplier?->id,
                'company_name' => $product->supplier?->company_name,
                'slug' => $product->supplier?->slug,
            ],
            'pricing_tiers' => $tiers
                ->map(fn ($tier): array => [
                    'id' => $tier->id,
                    'min_quantity' => (int) $tier->min_quantity,
                    'unit_price' => (float) $tier->unit_price,
                    'created_at' => $tier->created_at?->toDateTimeString(),
                    'updated_at' => $tier->updated_at?->toDateTimeString(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function ensureTierBelongsToProduct(Product $product, PricingTier $tier): void
    {
        if ((int) $tier->product_id !== (int) $product->id) {
            abort(404);
        }
    }

    private function ensureTierQuantityIsValid(Product $product, int $quantity): void
    {
        if ($quantity < (int) $product->moq) {
            throw ValidationException::withMessages([
                'min_quantity' => sprintf('Tier quantity must be at least the MOQ of %d.', $product->moq),
            ]);
        }
    }
}

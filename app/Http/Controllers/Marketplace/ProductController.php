<?php

namespace App\Http\Controllers\Marketplace;

use App\Domains\ECommerce\Enums\CartStatus;
use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Cart;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $categorySlug = trim((string) $request->query('category', ''));

        $query = Product::query()
            ->with(['supplier', 'category', 'images', 'pricingTiers'])
            ->where('status', ProductStatus::Active->value);

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('sku', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhereHas('supplier', fn ($supplier) => $supplier->where('company_name', 'like', '%'.$search.'%'));
            });
        }

        if ($categorySlug !== '') {
            $query->whereHas('category', fn ($category) => $category->where('slug', $categorySlug));
        }

        $products = $query
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Product $product): array => $this->presentProductCard($product));

        $featuredProducts = Product::query()
            ->with(['supplier', 'category', 'images', 'pricingTiers'])
            ->where('status', ProductStatus::Active->value)
            ->latest('published_at')
            ->limit(6)
            ->get()
            ->map(fn (Product $product): array => $this->presentProductCard($product))
            ->values()
            ->all();

        $categories = Category::query()
            ->select(['id', 'name', 'slug'])
            ->withCount([
                'products as active_products_count' => fn ($products) => $products->where('status', ProductStatus::Active->value),
            ])
            ->whereHas('products', fn ($products) => $products->where('status', ProductStatus::Active->value))
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'active_products_count' => (int) ($category->active_products_count ?? 0),
            ])
            ->values()
            ->all();

        return Inertia::render('Marketplace/Products/Index', [
            'filters' => [
                'search' => $search,
                'category' => $categorySlug,
            ],
            'cartCount' => $this->cartCount($request->user()),
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'products' => $products,
            'currency' => config('commerce.currency', 'BDT'),
        ]);
    }

    public function show(Request $request, Product $product): Response
    {
        $this->ensureVisible($request->user(), $product);

        $product->loadMissing(['supplier', 'category', 'images', 'pricingTiers']);

        $relatedProducts = Product::query()
            ->with(['supplier', 'category', 'images', 'pricingTiers'])
            ->where('status', ProductStatus::Active->value)
            ->whereKeyNot($product->id)
            ->where(function ($query) use ($product): void {
                if ($product->category_id) {
                    $query->where('category_id', $product->category_id);
                }

                if ($product->supplier_id) {
                    $query->orWhere('supplier_id', $product->supplier_id);
                }
            })
            ->latest('published_at')
            ->limit(8)
            ->get()
            ->map(fn (Product $related): array => $this->presentProductCard($related))
            ->values()
            ->all();

        $supplierProducts = Product::query()
            ->with(['supplier', 'category', 'images', 'pricingTiers'])
            ->where('status', ProductStatus::Active->value)
            ->where('supplier_id', $product->supplier_id)
            ->whereKeyNot($product->id)
            ->latest('published_at')
            ->limit(4)
            ->get()
            ->map(fn (Product $related): array => $this->presentProductCard($related))
            ->values()
            ->all();

        return Inertia::render('Marketplace/Products/Show', [
            'cartCount' => $this->cartCount($request->user()),
            'currency' => config('commerce.currency', 'BDT'),
            'defaultQuantity' => max(1, min((int) $product->moq, max(1, (int) $product->availableStock()))),
            'product' => ProductResource::make($product)->resolve($request),
            'relatedProducts' => $relatedProducts,
            'supplierProducts' => $supplierProducts,
            'isPurchasable' => $product->status === ProductStatus::Active && $product->availableStock() >= $product->moq,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentProductCard(Product $product): array
    {
        $product->loadMissing(['supplier', 'category', 'images', 'pricingTiers']);

        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'slug' => $product->slug,
            'name' => $product->name,
            'description' => Str::of(strip_tags((string) $product->description))->squish()->limit(140)->toString(),
            'base_price' => (float) $product->base_price,
            'moq' => (int) $product->moq,
            'available_stock' => (int) $product->availableStock(),
            'status' => $product->status->value,
            'primary_image_url' => $product->primaryImageUrl(),
            'supplier' => [
                'id' => $product->supplier?->id,
                'company_name' => $product->supplier?->company_name,
                'slug' => $product->supplier?->slug,
            ],
            'category' => [
                'id' => $product->category?->id,
                'name' => $product->category?->name,
                'slug' => $product->category?->slug,
            ],
            'pricing_tiers' => $product->pricingTiers
                ->sortBy('min_quantity')
                ->values()
                ->map(fn ($tier): array => [
                    'id' => $tier->id,
                    'min_quantity' => (int) $tier->min_quantity,
                    'unit_price' => (float) $tier->unit_price,
                ])
                ->all(),
        ];
    }

    private function cartCount(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return (int) Cart::query()
            ->where('user_id', $user->id)
            ->where('status', CartStatus::Active->value)
            ->withSum('items as items_count', 'quantity')
            ->first()
            ?->items_count ?? 0;
    }

    private function ensureVisible(?User $user, Product $product): void
    {
        if ($product->status === ProductStatus::Active) {
            return;
        }

        if ($user?->hasRole('admin')) {
            return;
        }

        if ($user?->hasRole('supplier') && (int) $product->supplier?->user_id === (int) $user->id) {
            return;
        }

        abort(404);
    }
}

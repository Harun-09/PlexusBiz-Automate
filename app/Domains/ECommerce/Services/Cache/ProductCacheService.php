<?php

namespace App\Domains\ECommerce\Services\Cache;

use App\Domains\ECommerce\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductCacheService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get product by ID with caching.
     */
    public function getProduct(int $id): ?Product
    {
        $cacheKey = "product_{$id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return Product::with(['supplier', 'category', 'media'])->find($id);
        });
    }

    /**
     * Get active products for the catalog with caching.
     */
    public function getActiveProducts(int $page = 1, int $perPage = 15)
    {
        $cacheKey = "products_active_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($perPage) {
            return Product::with(['supplier', 'category', 'media'])
                ->where('status', 'active')
                ->paginate($perPage);
        });
    }

    /**
     * Clear product cache (call this when a product is updated/deleted).
     */
    public function clearProductCache(int $id): void
    {
        Cache::forget("product_{$id}");
        // We might also need to clear paginated caches or use tags if supported (e.g., Redis)
        // Cache::tags(['products'])->flush();
    }
}

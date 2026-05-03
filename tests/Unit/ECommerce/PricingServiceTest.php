<?php

namespace Tests\Unit\ECommerce;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Services\PricingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_best_matching_bulk_pricing_tier(): void
    {
        $product = $this->product();

        PricingTier::create(['product_id' => $product->id, 'min_quantity' => 5, 'unit_price' => '95.00']);
        PricingTier::create(['product_id' => $product->id, 'min_quantity' => 10, 'unit_price' => '88.00']);

        $pricing = app(PricingService::class);

        $this->assertSame('100.00', $pricing->unitPrice($product, 2));
        $this->assertSame('95.00', $pricing->unitPrice($product, 7));
        $this->assertSame('88.00', $pricing->unitPrice($product, 12));
    }

    private function product(): Product
    {
        $user = User::factory()->create();
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'company_name' => 'Tier Supplier',
            'slug' => 'tier-supplier-'.Str::random(12),
            'status' => SupplierStatus::Approved,
            'contact_email' => $user->email,
            'approved_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'Parts',
            'slug' => 'parts-'.Str::random(12),
            'status' => 'active',
        ]);

        return Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'sku' => 'PART-'.Str::upper(Str::random(10)),
            'name' => 'Bulk Component',
            'slug' => 'bulk-component-'.Str::random(12),
            'base_price' => '100.00',
            'moq' => 2,
            'stock_quantity' => 100,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active,
            'published_at' => now(),
        ]);
    }
}

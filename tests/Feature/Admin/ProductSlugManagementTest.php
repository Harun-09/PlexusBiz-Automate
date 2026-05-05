<?php

namespace Tests\Feature\Admin;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSlugManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_product_slugs_are_unique_when_names_repeat(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->post('/admin/products', $this->productPayload($supplier, [
                'sku' => 'PX-SLUG-001',
                'name' => 'Duplicate Slug Product',
            ]))
            ->assertRedirect('/admin/products');

        $this->actingAs($admin)
            ->post('/admin/products', $this->productPayload($supplier, [
                'sku' => 'PX-SLUG-002',
                'name' => 'Duplicate Slug Product',
            ]))
            ->assertRedirect('/admin/products');

        $products = Product::query()
            ->where('name', 'Duplicate Slug Product')
            ->orderBy('id')
            ->get(['slug']);

        $this->assertCount(2, $products);
        $this->assertSame('duplicate-slug-product', $products->get(0)->slug);
        $this->assertSame('duplicate-slug-product-2', $products->get(1)->slug);
    }

    public function test_admin_product_slug_stays_unique_when_renaming_to_an_existing_name(): void
    {
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->post('/admin/products', $this->productPayload($supplier, [
                'sku' => 'PX-SLUG-010',
                'name' => 'Original Product',
            ]))
            ->assertRedirect('/admin/products');

        $this->actingAs($admin)
            ->post('/admin/products', $this->productPayload($supplier, [
                'sku' => 'PX-SLUG-011',
                'name' => 'Target Product',
            ]))
            ->assertRedirect('/admin/products');

        $product = Product::query()
            ->where('sku', 'PX-SLUG-011')
            ->firstOrFail();

        $this->actingAs($admin)
            ->put('/admin/products/'.$product->id, $this->productPayload($supplier, [
                'sku' => 'PX-SLUG-011',
                'name' => 'Original Product',
            ]))
            ->assertRedirect('/admin/products');

        $product->refresh();

        $this->assertSame('original-product-2', $product->slug);
    }

    private function productPayload(Supplier $supplier, array $overrides = []): array
    {
        return array_merge([
            'supplier_id' => $supplier->id,
            'sku' => 'PX-SLUG-001',
            'name' => 'Duplicate Slug Product',
            'description' => 'A product used to verify slug uniqueness.',
            'base_price' => 149.99,
            'moq' => 2,
            'bulk_price' => 129.99,
            'stock_quantity' => 42,
            'status' => ProductStatus::Active->value,
        ], $overrides);
    }
}

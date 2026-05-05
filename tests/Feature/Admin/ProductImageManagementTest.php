<?php

namespace Tests\Feature\Admin;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_replace_a_primary_product_image(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->seed(RbacSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->post('/admin/products', $this->productPayload($supplier, [
                'image' => UploadedFile::fake()->image('catalog-primary.jpg', 1400, 900),
            ]))
            ->assertRedirect('/admin/products');

        $product = Product::query()
            ->where('sku', 'PX-IMG-001')
            ->with('images')
            ->firstOrFail();

        $this->assertCount(1, $product->images);

        $primaryImage = $product->images->firstOrFail();
        $this->assertTrue($primaryImage->is_primary);
        $this->assertSame('Catalog Hero One', $primaryImage->alt_text);

        Storage::disk('local')->assertExists($primaryImage->originalPath());
        Storage::disk('public')->assertExists($primaryImage->publicPath());
        Storage::disk('public')->assertExists($primaryImage->thumbnailPath());
        Storage::disk('public')->assertExists($primaryImage->previewPath());

        $this->actingAs($admin)
            ->get('/admin/products/'.$product->id.'/edit')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/Products/Edit')
                ->where('product.primary_image_url', $primaryImage->url())
            );

        $this->actingAs($admin)
            ->post('/admin/products/'.$product->id, array_merge(
                $this->productPayload($supplier, [
                    'name' => 'Catalog Hero One Updated',
                    'description' => 'Updated product image and copy.',
                    'base_price' => 159.99,
                    'stock_quantity' => 30,
                ]),
                [
                    '_method' => 'put',
                    'image' => UploadedFile::fake()->image('catalog-replacement.jpg', 1600, 1000),
                ],
            ))
            ->assertRedirect('/admin/products');

        $product = Product::query()
            ->where('id', $product->id)
            ->with('images')
            ->firstOrFail();

        $this->assertSame('Catalog Hero One Updated', $product->name);
        $this->assertCount(1, $product->images);

        $replacementImage = $product->images->firstOrFail();
        $this->assertTrue($replacementImage->is_primary);
        $this->assertNotSame($primaryImage->id, $replacementImage->id);

        Storage::disk('local')->assertMissing($primaryImage->originalPath());
        Storage::disk('public')->assertMissing($primaryImage->publicPath());
        Storage::disk('public')->assertMissing($primaryImage->thumbnailPath());
        Storage::disk('public')->assertMissing($primaryImage->previewPath());

        Storage::disk('local')->assertExists($replacementImage->originalPath());
        Storage::disk('public')->assertExists($replacementImage->publicPath());
        Storage::disk('public')->assertExists($replacementImage->thumbnailPath());
        Storage::disk('public')->assertExists($replacementImage->previewPath());
    }

    private function productPayload(Supplier $supplier, array $overrides = []): array
    {
        return array_merge([
            'supplier_id' => $supplier->id,
            'sku' => 'PX-IMG-001',
            'name' => 'Catalog Hero One',
            'description' => 'A product that proves the admin image upload flow.',
            'base_price' => 149.99,
            'moq' => 2,
            'bulk_price' => 129.99,
            'stock_quantity' => 42,
            'status' => ProductStatus::Active->value,
        ], $overrides);
    }
}

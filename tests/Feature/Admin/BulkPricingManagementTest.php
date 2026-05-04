<?php

namespace Tests\Feature\Admin;

use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BulkPricingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_moq_and_pricing_tiers_from_a_dedicated_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@plexus.test')->firstOrFail();
        $product = Product::where('sku', 'PX-PUMP-100')->firstOrFail();
        $tier = $product->pricingTiers()->orderBy('min_quantity')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/bulk-pricing?product='.$product->id)
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Admin/BulkPricing/Index')
                ->where('selectedProduct.id', $product->id)
                ->where('selectedProduct.sku', $product->sku)
                ->where('selectedProduct.moq', $product->moq)
                ->has('selectedProduct.pricing_tiers', 2)
                ->where('selectedProduct.pricing_tiers.0.id', $tier->id));

        $this->actingAs($admin)
            ->put('/admin/bulk-pricing/'.$product->id, ['moq' => 4])
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'moq' => 4,
        ]);

        $this->actingAs($admin)
            ->post('/admin/bulk-pricing/'.$product->id.'/tiers', [
                'min_quantity' => 25,
                'unit_price' => 10350,
            ])
            ->assertRedirect();

        $newTier = PricingTier::where('product_id', $product->id)
            ->where('min_quantity', 25)
            ->firstOrFail();

        $this->assertDatabaseHas('pricing_tiers', [
            'id' => $newTier->id,
            'product_id' => $product->id,
            'min_quantity' => 25,
        ]);

        $this->actingAs($admin)
            ->put('/admin/bulk-pricing/'.$product->id.'/tiers/'.$newTier->id, [
                'min_quantity' => 30,
                'unit_price' => 10150,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pricing_tiers', [
            'id' => $newTier->id,
            'product_id' => $product->id,
            'min_quantity' => 30,
        ]);

        $this->actingAs($admin)
            ->delete('/admin/bulk-pricing/'.$product->id.'/tiers/'.$newTier->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('pricing_tiers', [
            'id' => $newTier->id,
        ]);
    }
}

<?php

namespace Tests\Feature\ECommerce;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\InventoryMovement;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Services\CartService;
use App\Domains\ECommerce\Services\CheckoutService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_invoice_items_and_deducts_inventory(): void
    {
        $buyer = User::factory()->create();
        $product = $this->product(stock: 20, moq: 2);
        PricingTier::create([
            'product_id' => $product->id,
            'min_quantity' => 5,
            'unit_price' => '90.00',
        ]);

        app(CartService::class)->addItem($buyer, $product, 5);

        $order = app(CheckoutService::class)->checkout($buyer);

        $this->assertSame('450.00', $order->grand_total);
        $this->assertSame(1, $order->items()->count());
        $this->assertNotNull($order->invoice);
        $this->assertSame(15, $product->refresh()->stock_quantity);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => '90.00',
            'total' => '450.00',
        ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $buyer->id,
            'converted_order_id' => $order->id,
            'status' => 'converted',
        ]);

        $this->assertSame(1, InventoryMovement::where('reference_id', $order->id)->count());
    }

    public function test_cart_rejects_quantity_below_product_moq(): void
    {
        $this->expectException(ValidationException::class);

        app(CartService::class)->addItem(
            User::factory()->create(),
            $this->product(stock: 10, moq: 3),
            2,
        );
    }

    private function product(int $stock, int $moq): Product
    {
        $supplierUser = User::factory()->create();
        $supplier = Supplier::create([
            'user_id' => $supplierUser->id,
            'company_name' => 'Supplier '.Str::random(8),
            'slug' => 'supplier-'.Str::random(12),
            'status' => SupplierStatus::Approved,
            'contact_email' => $supplierUser->email,
            'approved_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'Equipment',
            'slug' => 'equipment-'.Str::random(12),
            'status' => 'active',
        ]);

        return Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'sku' => 'SKU-'.Str::upper(Str::random(10)),
            'name' => 'Industrial Pump',
            'slug' => 'industrial-pump-'.Str::random(12),
            'base_price' => '100.00',
            'moq' => $moq,
            'stock_quantity' => $stock,
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active,
            'published_at' => now(),
        ]);
    }
}

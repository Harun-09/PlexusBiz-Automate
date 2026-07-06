<?php

namespace Tests\Feature;

use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Product;
use App\Domains\Tax\Models\TaxConfiguration;
use App\Domains\Tax\Services\VatCalculator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VatCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_vat_using_default_rate()
    {
        $user = User::factory()->create();
        $supplier = \App\Domains\ECommerce\Models\Supplier::factory()->create(['user_id' => $user->id]);
        $customer = \App\Domains\CRM\Models\Customer::factory()->create(['user_id' => $user->id]);
        TaxConfiguration::forceCreate(['region' => 'BD', 'tax_rate' => 15.00]);

        $product = Product::forceCreate(['name' => 'Test', 'sku' => 'TEST-01', 'slug' => 'test-01', 'base_price' => 100, 'moq' => 1, 'supplier_id' => $supplier->id]);

        $order = Order::forceCreate(['order_number' => 'ORD-01', 'buyer_id' => $user->id, 'customer_id' => $customer->id, 'status' => 'pending', 'grand_total' => 200]);
        OrderItem::forceCreate([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'supplier_id' => $supplier->id,
            'unit_price' => 100,
            'quantity' => 2,
            'total' => 200,
        ]);

        $calculator = new VatCalculator();
        $vat = $calculator->calculateTotalVatForOrder($order);

        $this->assertEquals(30.00, $vat);
    }

    public function test_calculates_vat_using_category_specific_rate()
    {
        $user = User::factory()->create();
        $supplier = \App\Domains\ECommerce\Models\Supplier::factory()->create(['user_id' => $user->id]);
        $customer = \App\Domains\CRM\Models\Customer::factory()->create(['user_id' => $user->id]);
        $category = Category::forceCreate(['name' => 'Electronics', 'slug' => 'electronics']);
        TaxConfiguration::forceCreate(['region' => 'BD', 'category_id' => $category->id, 'tax_rate' => 5.00]);
        TaxConfiguration::forceCreate(['region' => 'BD', 'tax_rate' => 15.00]);

        $product = Product::forceCreate(['name' => 'Phone', 'sku' => 'PHN-01', 'slug' => 'phn-01', 'base_price' => 100, 'moq' => 1, 'category_id' => $category->id, 'supplier_id' => $supplier->id]);

        $order = Order::forceCreate(['order_number' => 'ORD-02', 'buyer_id' => $user->id, 'customer_id' => $customer->id, 'status' => 'pending', 'grand_total' => 200]);
        OrderItem::forceCreate([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'supplier_id' => $supplier->id,
            'unit_price' => 100,
            'quantity' => 2,
            'total' => 200,
        ]);

        $calculator = new VatCalculator();
        $vat = $calculator->calculateTotalVatForOrder($order);

        $this->assertEquals(10.00, $vat);
    }
}

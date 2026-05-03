<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Services\InventoryService;
use App\Models\User;
use Illuminate\Database\Seeder;

class ECommerceSeeder extends Seeder
{
    public function run(): void
    {
        $supplierUser = User::where('email', 'supplier@plexus.test')->firstOrFail();

        $supplier = Supplier::updateOrCreate(
            ['user_id' => $supplierUser->id],
            [
                'company_name' => 'Plexus Industrial Supply',
                'slug' => 'plexus-industrial-supply',
                'status' => SupplierStatus::Approved,
                'contact_email' => 'supplier@plexus.test',
                'phone' => '+8801700000000',
                'address' => [
                    'line_1' => 'House 12, Road 8',
                    'city' => 'Dhaka',
                    'country' => 'Bangladesh',
                ],
                'approved_at' => now(),
            ],
        );

        $category = Category::updateOrCreate(
            ['slug' => 'industrial-equipment'],
            [
                'name' => 'Industrial Equipment',
                'status' => 'active',
                'description' => 'B2B equipment and operational supplies.',
            ],
        );

        $product = Product::updateOrCreate(
            ['sku' => 'PX-PUMP-100'],
            [
                'supplier_id' => $supplier->id,
                'category_id' => $category->id,
                'name' => 'Commercial Water Pump 100L',
                'slug' => 'commercial-water-pump-100l',
                'description' => 'High-volume commercial water pump for supplier-managed B2B orders.',
                'base_price' => '12500.00',
                'moq' => 2,
                'status' => ProductStatus::Active,
                'published_at' => now(),
            ],
        );

        PricingTier::updateOrCreate(
            ['product_id' => $product->id, 'min_quantity' => 5],
            ['unit_price' => '11800.00'],
        );

        PricingTier::updateOrCreate(
            ['product_id' => $product->id, 'min_quantity' => 10],
            ['unit_price' => '11000.00'],
        );

        if ($product->inventoryMovements()->doesntExist()) {
            app(InventoryService::class)->stockIn($product, 100, $supplierUser, 'Initial supplier inventory');
        }
    }
}

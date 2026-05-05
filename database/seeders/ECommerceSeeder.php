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
use Illuminate\Support\Facades\Hash;

class ECommerceSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = collect([
            [
                'name' => 'Supplier User',
                'email' => 'supplier@plexus.test',
                'company' => 'Plexus Industrial Supply',
                'phone' => '+8801700000000',
                'address' => [
                    'line_1' => 'House 12, Road 8',
                    'city' => 'Dhaka',
                    'country' => 'Bangladesh',
                ],
            ],
            [
                'name' => 'Dhaka Tools Ltd',
                'email' => 'supplier2@dhakatools.test',
                'company' => 'Dhaka Tools & Equipment',
                'phone' => '+8801712345601',
                'address' => [
                    'line_1' => 'Sector 3, Road 14',
                    'city' => 'Dhaka',
                    'country' => 'Bangladesh',
                ],
            ],
            [
                'name' => 'Bangladesh Textiles',
                'email' => 'supplier3@bdtex.test',
                'company' => 'BD Textile Mills',
                'phone' => '+8801712345602',
                'address' => [
                    'line_1' => 'Plot 18, Industrial Area',
                    'city' => 'Gazipur',
                    'country' => 'Bangladesh',
                ],
            ],
        ])->map(function (array $supplier): Supplier {
            $user = User::firstOrCreate(
                ['email' => $supplier['email']],
                [
                    'name' => $supplier['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );

            if (! $user->hasRole('supplier')) {
                $user->assignRole('supplier');
            }

            return Supplier::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $supplier['company'],
                    'slug' => str($supplier['company'])->slug()->toString(),
                    'status' => SupplierStatus::Approved,
                    'contact_email' => $supplier['email'],
                    'phone' => $supplier['phone'],
                    'address' => $supplier['address'],
                    'approved_at' => now(),
                ],
            );
        })->values();

        $categories = collect([
            ['name' => 'Industrial Equipment', 'slug' => 'industrial-equipment'],
            ['name' => 'Warehouse Logistics', 'slug' => 'warehouse-logistics'],
            ['name' => 'Office Furniture', 'slug' => 'office-furniture'],
        ])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'status' => 'active',
                    'description' => 'B2B wholesale '.$category['name'],
                ],
            ),
        ]);

        $products = [
            [
                'sku' => 'PX-PUMP-100',
                'name' => 'Commercial Water Pump 100L',
                'slug' => 'commercial-water-pump-100l',
                'supplier_index' => 0,
                'category' => 'industrial-equipment',
                'price' => '12500.00',
                'moq' => 2,
                'stock' => 100,
                'description' => 'High-volume commercial water pump for supplier-managed B2B orders.',
            ],
            [
                'sku' => 'PX-LOG-101',
                'name' => 'Industrial Dock Plate 1500lb',
                'slug' => 'industrial-dock-plate-1500lb',
                'supplier_index' => 1,
                'category' => 'warehouse-logistics',
                'price' => '12000.00',
                'moq' => 5,
                'stock' => 60,
                'description' => 'Heavy-duty dock plate for warehouse loading and supplier fulfillment.',
            ],
            [
                'sku' => 'PX-OFF-102',
                'name' => 'Wholesale Filing Cabinet Pro',
                'slug' => 'wholesale-filing-cabinet-pro',
                'supplier_index' => 2,
                'category' => 'office-furniture',
                'price' => '8500.00',
                'moq' => 3,
                'stock' => 40,
                'description' => 'Bulk-ready office storage furniture for corporate procurement.',
            ],
        ];

        foreach ($products as $productData) {
            $supplier = $suppliers[$productData['supplier_index']];
            $supplierUser = $supplier->user;

            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'supplier_id' => $supplier->id,
                    'category_id' => $categories[$productData['category']]->id,
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'description' => $productData['description'],
                    'base_price' => $productData['price'],
                    'moq' => $productData['moq'],
                    'status' => ProductStatus::Active,
                    'published_at' => now(),
                ],
            );

            PricingTier::updateOrCreate(
                ['product_id' => $product->id, 'min_quantity' => $productData['moq'] * 2],
                ['unit_price' => (string) round((float) $productData['price'] * 0.94, 2)],
            );

            PricingTier::updateOrCreate(
                ['product_id' => $product->id, 'min_quantity' => $productData['moq'] * 5],
                ['unit_price' => (string) round((float) $productData['price'] * 0.88, 2)],
            );

            if ($product->inventoryMovements()->doesntExist()) {
                app(InventoryService::class)->stockIn($product, $productData['stock'], $supplierUser, 'Initial supplier inventory');
            }
        }
    }
}

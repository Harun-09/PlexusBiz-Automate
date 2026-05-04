<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\PricingTier;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use App\Domains\ECommerce\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BulkProductSeeder extends Seeder
{
    private array $categories = [
        [
            'name' => 'Industrial Safety & PPE',
            'slug' => 'industrial-safety-ppe',
            'products' => [
                ['name' => 'Heavy-Duty Safety Helmet ABS', 'base' => 450, 'moq' => 50],
                ['name' => 'Welding Mask Auto-Darkening', 'base' => 1200, 'moq' => 20],
                ['name' => 'Cut-Resistant Gloves Level 5', 'base' => 350, 'moq' => 100],
                ['name' => 'Safety Goggles Anti-Fog', 'base' => 280, 'moq' => 100],
                ['name' => 'Ear Protection Earmuffs 30dB', 'base' => 650, 'moq' => 40],
                ['name' => 'Dust Mask N95 Certified', 'base' => 45, 'moq' => 500],
                ['name' => 'Chemical Resistant Suit', 'base' => 1800, 'moq' => 25],
                ['name' => 'Safety Harness Full Body', 'base' => 3200, 'moq' => 15],
                ['name' => 'Reflective Safety Vest High-Viz', 'base' => 180, 'moq' => 200],
                ['name' => 'Steel Toe Safety Boots', 'base' => 2800, 'moq' => 30],
                ['name' => 'Fire Extinguisher 6KG CO2', 'base' => 4200, 'moq' => 10],
                ['name' => 'First Aid Kit Industrial 50-Person', 'base' => 3500, 'moq' => 20],
            ],
        ],
        [
            'name' => 'Office & Stationery Supplies',
            'slug' => 'office-stationery',
            'products' => [
                ['name' => 'Premium A4 Copy Paper 80gsm (Ream)', 'base' => 380, 'moq' => 100],
                ['name' => 'Gel Pen Blue 0.5mm (Box 50)', 'base' => 650, 'moq' => 50],
                ['name' => 'Sticky Notes 3x3 Neon Colors', 'base' => 120, 'moq' => 200],
                ['name' => 'Document Envelope A4 (Pack 100)', 'base' => 280, 'moq' => 100],
                ['name' => 'File Folder Cardboard (Box 50)', 'base' => 450, 'moq' => 50],
                ['name' => 'Stapler Heavy Duty 40 Sheets', 'base' => 380, 'moq' => 30],
                ['name' => 'Printer Ink Cartridge HP Compatible', 'base' => 850, 'moq' => 25],
                ['name' => 'Whiteboard Marker Set 8 Colors', 'base' => 320, 'moq' => 60],
                ['name' => 'Document Binder Clip Assorted', 'base' => 180, 'moq' => 150],
                ['name' => 'Letter Tray Stackable Plastic', 'base' => 280, 'moq' => 40],
                ['name' => 'Desk Organizer 5-Compartment', 'base' => 520, 'moq' => 35],
                ['name' => 'Ballpoint Pen Black (Box 100)', 'base' => 480, 'moq' => 50],
            ],
        ],
        [
            'name' => 'Packaging & Shipping Materials',
            'slug' => 'packaging-shipping',
            'products' => [
                ['name' => 'Cardboard Box 12x12x12 (Bundle 25)', 'base' => 850, 'moq' => 50],
                ['name' => 'Bubble Wrap Roll 100m x 500mm', 'base' => 1200, 'moq' => 20],
                ['name' => 'Packing Tape Clear 2-inch (Pack 36)', 'base' => 680, 'moq' => 40],
                ['name' => 'Stretch Wrap Film 18-inch (Roll)', 'base' => 1450, 'moq' => 15],
                ['name' => 'Corrugated Mailer Box Small (Pack 50)', 'base' => 1200, 'moq' => 30],
                ['name' => 'Packing Peanuts Biodegradable (Bag)', 'base' => 950, 'moq' => 25],
                ['name' => 'Shipping Label A4 Sticker (Pack 100)', 'base' => 450, 'moq' => 50],
                ['name' => 'Edge Protector Cardboard (Pack 100)', 'base' => 780, 'moq' => 30],
                ['name' => 'Poly Mailer Bag 10x13 (Pack 100)', 'base' => 350, 'moq' => 100],
                ['name' => 'Strapping Band PP 12mm (Roll)', 'base' => 1680, 'moq' => 12],
                ['name' => 'Carton Sealer Machine Manual', 'base' => 4500, 'moq' => 8],
                ['name' => 'Void Fill Paper Roll 500m', 'base' => 2200, 'moq' => 10],
            ],
        ],
        [
            'name' => 'IT & Computer Accessories',
            'slug' => 'it-computer-accessories',
            'products' => [
                ['name' => 'Wireless Mouse Ergonomic', 'base' => 650, 'moq' => 50],
                ['name' => 'Mechanical Keyboard RGB', 'base' => 2800, 'moq' => 20],
                ['name' => 'USB-C Hub 7-in-1 Adapter', 'base' => 1200, 'moq' => 40],
                ['name' => 'Webcam HD 1080p AutoFocus', 'base' => 1800, 'moq' => 25],
                ['name' => 'Laptop Stand Adjustable Aluminum', 'base' => 950, 'moq' => 35],
                ['name' => 'Monitor Stand Riser Mesh Metal', 'base' => 750, 'moq' => 40],
                ['name' => 'Cable Management Box Set', 'base' => 450, 'moq' => 60],
                ['name' => 'HDMI Cable 4K 3ft (Pack 10)', 'base' => 1200, 'moq' => 25],
                ['name' => 'Desk Pad Large Leather', 'base' => 850, 'moq' => 30],
                ['name' => 'Headset Noise Cancelling USB', 'base' => 2200, 'moq' => 20],
                ['name' => 'Power Strip Surge Protector 8-Outlet', 'base' => 680, 'moq' => 45],
                ['name' => 'Laptop Sleeve 15.6-inch Felt', 'base' => 380, 'moq' => 80],
            ],
        ],
        [
            'name' => 'Cleaning & Maintenance',
            'slug' => 'cleaning-maintenance',
            'products' => [
                ['name' => 'Floor Mop Industrial 24-inch', 'base' => 450, 'moq' => 40],
                ['name' => 'Disinfectant Liquid 5L Concentrate', 'base' => 650, 'moq' => 30],
                ['name' => 'Glass Cleaner Spray 500ml', 'base' => 180, 'moq' => 100],
                ['name' => 'Trash Bag Heavy Duty 33gal (Roll)', 'base' => 280, 'moq' => 150],
                ['name' => 'Microfiber Cleaning Cloth (Pack 50)', 'base' => 320, 'moq' => 80],
                ['name' => 'Toilet Paper Roll 2-Ply (Pack 48)', 'base' => 580, 'moq' => 50],
                ['name' => 'Hand Soap Dispenser Refill 5L', 'base' => 450, 'moq' => 40],
                ['name' => 'Air Freshener Automatic Dispenser', 'base' => 850, 'moq' => 25],
                ['name' => 'Vacuum Cleaner Industrial Wet/Dry', 'base' => 12000, 'moq' => 5],
                ['name' => 'Scrub Brush Heavy Duty Plastic', 'base' => 120, 'moq' => 200],
                ['name' => 'Paper Towel Roll Center-Pull (Case)', 'base' => 1200, 'moq' => 20],
                ['name' => 'Bleach Chlorine 5L Commercial', 'base' => 380, 'moq' => 60],
            ],
        ],
        [
            'name' => 'Industrial Tools & Equipment',
            'slug' => 'industrial-tools',
            'products' => [
                ['name' => 'Cordless Drill Driver 20V', 'base' => 6500, 'moq' => 12],
                ['name' => 'Angle Grinder 4-1/2 inch 800W', 'base' => 3200, 'moq' => 15],
                ['name' => 'Wrench Set Chrome 24-Piece', 'base' => 2800, 'moq' => 20],
                ['name' => 'Screwdriver Set Precision 6-Piece', 'base' => 450, 'moq' => 50],
                ['name' => 'Tool Box Metal 24-inch with Tray', 'base' => 2200, 'moq' => 15],
                ['name' => 'Measuring Tape Steel 25ft (Pack 10)', 'base' => 1200, 'moq' => 30],
                ['name' => 'Level Spirit Magnetic 12-inch', 'base' => 650, 'moq' => 40],
                ['name' => 'Pliers Set 3-Piece Heavy Duty', 'base' => 950, 'moq' => 35],
                ['name' => 'Hammer Claw Fiberglass 16oz', 'base' => 380, 'moq' => 60],
                ['name' => 'Cable Cutter Heavy Duty 10-inch', 'base' => 850, 'moq' => 30],
                ['name' => 'Torque Wrench 1/2-inch Drive', 'base' => 3800, 'moq' => 12],
                ['name' => 'Multimeter Digital Auto-Ranging', 'base' => 1800, 'moq' => 25],
            ],
        ],
        [
            'name' => 'Furniture & Fixtures',
            'slug' => 'furniture-fixtures',
            'products' => [
                ['name' => 'Ergonomic Office Chair Mesh Back', 'base' => 8500, 'moq' => 10],
                ['name' => 'Standing Desk Electric Adjustable', 'base' => 28000, 'moq' => 5],
                ['name' => 'Filing Cabinet 4-Drawer Steel', 'base' => 12000, 'moq' => 8],
                ['name' => 'Bookshelf 5-Tier Wood 72-inch', 'base' => 4500, 'moq' => 12],
                ['name' => 'Conference Table 8ft Modern', 'base' => 25000, 'moq' => 3],
                ['name' => 'Visitor Chair Stackable Plastic', 'base' => 1800, 'moq' => 20],
                ['name' => 'Whiteboard Magnetic 48x36 Mobile', 'base' => 6500, 'moq' => 10],
                ['name' => 'Partition Divider 72-inch Fabric', 'base' => 4200, 'moq' => 15],
                ['name' => 'Coat Rack Metal Freestanding', 'base' => 850, 'moq' => 25],
                ['name' => 'Shoe Rack Metal 4-Tier', 'base' => 650, 'moq' => 40],
                ['name' => 'Cabinet Lock Keyed Alike (Pack 10)', 'base' => 450, 'moq' => 50],
                ['name' => 'Chair Mat Hard Floor 48x36', 'base' => 1200, 'moq' => 30],
            ],
        ],
        [
            'name' => 'Electrical & Lighting',
            'slug' => 'electrical-lighting',
            'products' => [
                ['name' => 'LED Bulb 9W Warm White (Pack 10)', 'base' => 680, 'moq' => 50],
                ['name' => 'LED Panel Light 2x2 40W', 'base' => 2800, 'moq' => 20],
                ['name' => 'Extension Cord 15m Heavy Duty', 'base' => 850, 'moq' => 35],
                ['name' => 'Emergency Light LED Exit Sign', 'base' => 1800, 'moq' => 20],
                ['name' => 'Motion Sensor Light Indoor', 'base' => 650, 'moq' => 40],
                ['name' => 'Work Light LED 50W Portable', 'base' => 1200, 'moq' => 25],
                ['name' => 'Cable Tie Nylon 8-inch (Pack 1000)', 'base' => 380, 'moq' => 80],
                ['name' => 'Wall Socket 3-Pin Universal', 'base' => 120, 'moq' => 200],
                ['name' => 'MCB Circuit Breaker 32A', 'base' => 450, 'moq' => 50],
                ['name' => 'LED Strip Light 5m RGB', 'base' => 950, 'moq' => 30],
                ['name' => 'Voltage Stabilizer 1000VA', 'base' => 4500, 'moq' => 15],
                ['name' => 'Solar Flood Light 100W', 'base' => 6500, 'moq' => 10],
            ],
        ],
        [
            'name' => 'Warehouse & Storage',
            'slug' => 'warehouse-storage',
            'products' => [
                ['name' => 'Pallet Rack Upright Frame 16ft', 'base' => 8500, 'moq' => 12],
                ['name' => 'Wire Shelf 48x18 Chrome', 'base' => 2800, 'moq' => 20],
                ['name' => 'Storage Bin Stackable 50L', 'base' => 650, 'moq' => 50],
                ['name' => 'Hand Truck Dolly 300kg Capacity', 'base' => 3200, 'moq' => 15],
                ['name' => 'Platform Trolley Foldable 150kg', 'base' => 4500, 'moq' => 12],
                ['name' => 'Barcode Scanner USB Handheld', 'base' => 2800, 'moq' => 20],
                ['name' => 'Label Printer Thermal 4-inch', 'base' => 8500, 'moq' => 10],
                ['name' => 'Safety Mirror Convex 18-inch', 'base' => 1800, 'moq' => 20],
                ['name' => 'Dock Plate Aluminum 1500lb', 'base' => 12000, 'moq' => 5],
                ['name' => 'Pallet Jack Manual 5500lb', 'base' => 18000, 'moq' => 3],
                ['name' => 'Stretch Wrap Dispenser', 'base' => 850, 'moq' => 30],
                ['name' => 'Inventory Tag Numbered (Pack 500)', 'base' => 450, 'moq' => 50],
            ],
        ],
    ];

    public function run(): void
    {
        $supplier = Supplier::first();
        
        if (! $supplier) {
            $this->command->warn('No supplier found. Please run RbacSeeder first.');
            return;
        }

        $this->command->info('Creating 108 professional B2B products...');
        $bar = $this->command->getOutput()->createProgressBar(count($this->categories) * 12);
        $bar->start();

        $productCount = 0;

        foreach ($this->categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'status' => 'active',
                    'description' => 'Professional B2B wholesale ' . $categoryData['name'],
                ]
            );

            foreach ($categoryData['products'] as $productData) {
                $sku = $this->generateSku($categoryData['slug'], $productCount);
                
                $product = Product::firstOrCreate(
                    ['sku' => $sku],
                    [
                        'supplier_id' => $supplier->id,
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'slug' => Str::slug($productData['name']) . '-' . $productCount,
                        'description' => $this->generateDescription($productData['name'], $categoryData['name']),
                        'base_price' => $productData['base'],
                        'moq' => $productData['moq'],
                        'status' => ProductStatus::Active,
                        'published_at' => now()->subDays(rand(1, 60)),
                    ]
                );

                // Add bulk pricing tiers
                $this->createPricingTiers($product, $productData['base'], $productData['moq']);

                // Seed inventory
                if ($product->inventoryMovements()->doesntExist()) {
                    $stock = $productData['moq'] * rand(10, 50);
                    app(InventoryService::class)->stockIn(
                        $product, 
                        $stock, 
                        $supplier->user, 
                        'Initial bulk inventory seed'
                    );
                }

                $productCount++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->command->info("\n{$productCount} products created successfully!");
    }

    private function generateSku(string $categorySlug, int $index): string
    {
        $prefix = strtoupper(substr(str_replace('-', '', $categorySlug), 0, 3));
        return 'PX-' . $prefix . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generateDescription(string $productName, string $categoryName): string
    {
        $features = [
            'Premium quality materials ensure long-lasting durability',
            'Manufactured to meet international safety standards',
            'Bulk pricing available for enterprise orders',
            'Fast shipping from local warehouses',
            '24-month warranty included with all purchases',
            'Compatible with industry-standard equipment',
            'Designed for heavy-duty commercial use',
            'Eco-friendly materials and packaging',
            'Tested and certified for professional applications',
            'Backed by our satisfaction guarantee',
        ];

        $randomFeatures = array_slice($features, 0, rand(3, 5));
        
        return "Professional-grade {$productName} for B2B wholesale. " . 
               "Ideal for {$categoryName} applications. " .
               implode(' ', $randomFeatures) .
               " Contact us for volume pricing and customization options.";
    }

    private function createPricingTiers(Product $product, float $basePrice, int $moq): void
    {
        // Tier 1: 2x MOQ = 10% off
        PricingTier::firstOrCreate(
            ['product_id' => $product->id, 'min_quantity' => $moq * 2],
            ['unit_price' => round($basePrice * 0.90, 2)]
        );

        // Tier 2: 5x MOQ = 15% off
        PricingTier::firstOrCreate(
            ['product_id' => $product->id, 'min_quantity' => $moq * 5],
            ['unit_price' => round($basePrice * 0.85, 2)]
        );

        // Tier 3: 10x MOQ = 20% off
        PricingTier::firstOrCreate(
            ['product_id' => $product->id, 'min_quantity' => $moq * 10],
            ['unit_price' => round($basePrice * 0.80, 2)]
        );
    }
}

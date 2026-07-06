<?php

namespace Tests\Feature;

use App\Domains\Inventory\Models\InventoryBatch;
use App\Domains\Inventory\Models\StockLocation;
use App\Domains\Manufacturing\Models\BillOfMaterial;
use App\Domains\Manufacturing\Models\BomItem;
use App\Domains\Manufacturing\Services\MrpEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MrpEngineTest extends TestCase
{
    use RefreshDatabase;

    private function seedDependencies()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('users')->insert(['id' => 1, 'name' => 'test', 'email' => 'mrp@test.com', 'password' => 'test', 'status' => 'active']);
        DB::table('suppliers')->insert(['id' => 1, 'user_id' => 1, 'company_name' => 'Test Supplier', 'slug' => 'test-supplier', 'status' => 'approved']);
        DB::table('warehouses')->insert(['id' => 1, 'supplier_id' => 1, 'name' => 'Main Warehouse']);

        StockLocation::create(['id' => 1, 'warehouse_id' => 1, 'zone' => 'A']);
    }

    public function test_mrp_detects_shortages_correctly()
    {
        $this->seedDependencies();

        // BOM: 1 Bicycle needs 2 Wheels (product_id=10) and 1 Frame (product_id=11)
        $bom = BillOfMaterial::create(['name' => 'Bicycle', 'product_id' => 100]);
        BomItem::create(['bill_of_material_id' => $bom->id, 'raw_material_product_id' => 10, 'quantity_required' => 2]);
        BomItem::create(['bill_of_material_id' => $bom->id, 'raw_material_product_id' => 11, 'quantity_required' => 1]);

        // We have 5 Wheels and 0 Frames in stock
        InventoryBatch::create([
            'product_id' => 10, 'stock_location_id' => 1, 'batch_number' => 'WHEEL-001',
            'initial_quantity' => 5, 'available_quantity' => 5, 'unit_cost' => 50,
        ]);

        // Want to build 10 Bicycles -> Need 20 Wheels, 10 Frames
        $engine = new MrpEngine();
        $shortages = $engine->calculateDependentDemand($bom->id, 10);

        // Wheels: need 20, have 5 -> shortage 15
        // Frames: need 10, have 0 -> shortage 10
        $this->assertCount(2, $shortages);
        $this->assertEquals(15, $shortages[0]['shortage_quantity']);
        $this->assertEquals(10, $shortages[1]['shortage_quantity']);

        // Also verify a draft PO was auto-created
        $this->assertDatabaseHas('purchase_orders', ['status' => 'pending']);
        $this->assertDatabaseHas('purchase_order_items', ['product_id' => 10, 'quantity' => 15]);
        $this->assertDatabaseHas('purchase_order_items', ['product_id' => 11, 'quantity' => 10]);
    }

    public function test_mrp_returns_no_shortages_when_stock_is_sufficient()
    {
        $this->seedDependencies();

        $bom = BillOfMaterial::create(['name' => 'Widget', 'product_id' => 200]);
        BomItem::create(['bill_of_material_id' => $bom->id, 'raw_material_product_id' => 20, 'quantity_required' => 1]);

        InventoryBatch::create([
            'product_id' => 20, 'stock_location_id' => 1, 'batch_number' => 'PART-001',
            'initial_quantity' => 100, 'available_quantity' => 100, 'unit_cost' => 10,
        ]);

        $engine = new MrpEngine();
        $shortages = $engine->calculateDependentDemand($bom->id, 5);

        $this->assertCount(0, $shortages);
    }
}

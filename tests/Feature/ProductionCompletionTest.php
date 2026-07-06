<?php

namespace Tests\Feature;

use App\Domains\Inventory\Models\InventoryBatch;
use App\Domains\Inventory\Models\StockLocation;
use App\Domains\Manufacturing\Models\BillOfMaterial;
use App\Domains\Manufacturing\Models\BomItem;
use App\Domains\Manufacturing\Models\ProductionOrder;
use App\Domains\Manufacturing\Services\ProductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionCompletionTest extends TestCase
{
    use RefreshDatabase;

    private function seedDependencies()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('users')->insert(['id' => 1, 'name' => 'test', 'email' => 'prod@test.com', 'password' => 'test', 'status' => 'active']);
        DB::table('suppliers')->insert(['id' => 1, 'user_id' => 1, 'company_name' => 'Test Supplier', 'slug' => 'test-supplier', 'status' => 'approved']);
        DB::table('warehouses')->insert(['id' => 1, 'supplier_id' => 1, 'name' => 'Main Warehouse']);

        StockLocation::create(['id' => 1, 'warehouse_id' => 1, 'zone' => 'A']);
    }

    public function test_completing_production_deducts_raw_materials_and_creates_finished_goods()
    {
        $this->seedDependencies();

        // BOM: 1 Table needs 4 Legs (product_id=30) and 1 Top (product_id=31)
        $bom = BillOfMaterial::create(['name' => 'Table', 'product_id' => 300, 'produced_quantity' => 1]);
        BomItem::create(['bill_of_material_id' => $bom->id, 'raw_material_product_id' => 30, 'quantity_required' => 4]);
        BomItem::create(['bill_of_material_id' => $bom->id, 'raw_material_product_id' => 31, 'quantity_required' => 1]);

        // Stock: 20 Legs, 5 Tops
        InventoryBatch::create([
            'product_id' => 30, 'stock_location_id' => 1, 'batch_number' => 'LEG-001',
            'initial_quantity' => 20, 'available_quantity' => 20, 'unit_cost' => 25,
        ]);
        InventoryBatch::create([
            'product_id' => 31, 'stock_location_id' => 1, 'batch_number' => 'TOP-001',
            'initial_quantity' => 5, 'available_quantity' => 5, 'unit_cost' => 100,
        ]);

        // Production Order: Build 3 Tables -> needs 12 Legs, 3 Tops
        $order = ProductionOrder::create([
            'order_number' => 'PO-001',
            'bill_of_material_id' => $bom->id,
            'target_quantity' => 3,
            'status' => 'in_progress',
            'start_date' => now(),
        ]);

        $service = new ProductionService();
        $completedOrder = $service->completeProductionOrder($order);

        $this->assertEquals('completed', $completedOrder->status);

        // Legs: 20 - 12 = 8
        $this->assertEquals(8, InventoryBatch::where('batch_number', 'LEG-001')->first()->available_quantity);
        // Tops: 5 - 3 = 2
        $this->assertEquals(2, InventoryBatch::where('batch_number', 'TOP-001')->first()->available_quantity);

        // Finished goods (Table, product_id=300) should now exist
        $finishedBatch = InventoryBatch::where('product_id', 300)->first();
        $this->assertNotNull($finishedBatch);
        $this->assertEquals(3, $finishedBatch->available_quantity);

        // Stock movements should be recorded
        $this->assertDatabaseHas('stock_movements', ['type' => 'out', 'reference_type' => 'ProductionConsumption']);
        $this->assertDatabaseHas('stock_movements', ['type' => 'in', 'reference_type' => 'ProductionOrder']);
    }

    public function test_production_fails_with_insufficient_raw_materials()
    {
        $this->seedDependencies();

        $bom = BillOfMaterial::create(['name' => 'Chair', 'product_id' => 400, 'produced_quantity' => 1]);
        BomItem::create(['bill_of_material_id' => $bom->id, 'raw_material_product_id' => 40, 'quantity_required' => 4]);

        // Only 2 legs in stock but need 40 (10 chairs * 4 legs)
        InventoryBatch::create([
            'product_id' => 40, 'stock_location_id' => 1, 'batch_number' => 'CHAIRLEG-001',
            'initial_quantity' => 2, 'available_quantity' => 2, 'unit_cost' => 15,
        ]);

        $order = ProductionOrder::create([
            'order_number' => 'PO-002',
            'bill_of_material_id' => $bom->id,
            'target_quantity' => 10,
            'status' => 'in_progress',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insufficient raw material");

        $service = new ProductionService();
        $service->completeProductionOrder($order);
    }
}

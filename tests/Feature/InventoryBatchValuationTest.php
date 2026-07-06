<?php

namespace Tests\Feature;

use App\Domains\Inventory\Models\InventoryBatch;
use App\Domains\Inventory\Models\StockLocation;
use App\Domains\Inventory\Services\ValuationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InventoryBatchValuationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fifo_valuation_deducts_oldest_stock_first()
    {
        DB::table('users')->insert([
            'id' => 1, 'name' => 'test', 'email' => 'test@test.com', 'password' => 'test', 'status' => 'active'
        ]);
        
        DB::table('suppliers')->insert([
            'id' => 1, 'user_id' => 1, 'company_name' => 'Test Supplier', 'slug' => 'test-supplier', 'status' => 'approved'
        ]);

        DB::table('warehouses')->insert([
            'id' => 1, 'supplier_id' => 1, 'name' => 'Main Warehouse'
        ]);

        $location = StockLocation::create([
            'warehouse_id' => 1,
            'zone' => 'A'
        ]);

        $batch1 = InventoryBatch::create([
            'product_id' => 1,
            'stock_location_id' => $location->id,
            'batch_number' => 'B001',
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'unit_cost' => 100,
            'created_at' => now()->subDays(5) // Oldest
        ]);

        $batch2 = InventoryBatch::create([
            'product_id' => 1,
            'stock_location_id' => $location->id,
            'batch_number' => 'B002',
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'unit_cost' => 150,
            'created_at' => now()->subDays(1) // Newest
        ]);

        $engine = new ValuationEngine();
        $result = $engine->deductStock(1, 15, 'FIFO');

        // Should take 10 from B001 (Cost 100) and 5 from B002 (Cost 150)
        // Total COGS = (10 * 100) + (5 * 150) = 1000 + 750 = 1750
        $this->assertEquals(1750, $result['total_cogs']);

        $this->assertEquals(0, $batch1->fresh()->available_quantity);
        $this->assertEquals(5, $batch2->fresh()->available_quantity);
        
        $this->assertDatabaseHas('stock_movements', [
            'inventory_batch_id' => $batch1->id,
            'quantity' => 10
        ]);
        
        $this->assertDatabaseHas('stock_movements', [
            'inventory_batch_id' => $batch2->id,
            'quantity' => 5
        ]);
    }
}

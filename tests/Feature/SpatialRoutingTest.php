<?php

namespace Tests\Feature;

use App\Domains\Inventory\Models\StockLocation;
use App\Domains\Inventory\Services\SpatialRoutingService;
use Tests\TestCase;

class SpatialRoutingTest extends TestCase
{
    public function test_spatial_routing_sorts_correctly()
    {
        $locations = collect([
            new StockLocation(['zone' => 'B', 'aisle' => '1', 'rack' => '1', 'bin' => '1']),
            new StockLocation(['zone' => 'A', 'aisle' => '2', 'rack' => '1', 'bin' => '1']),
            new StockLocation(['zone' => 'A', 'aisle' => '1', 'rack' => '2', 'bin' => '2']),
            new StockLocation(['zone' => 'A', 'aisle' => '1', 'rack' => '1', 'bin' => '2']),
            new StockLocation(['zone' => 'A', 'aisle' => '1', 'rack' => '1', 'bin' => '1']),
        ]);

        $service = new SpatialRoutingService();
        $sorted = $service->generatePickPath($locations);

        $this->assertEquals('A', $sorted[0]->zone);
        $this->assertEquals('1', $sorted[0]->aisle);
        $this->assertEquals('1', $sorted[0]->rack);
        $this->assertEquals('1', $sorted[0]->bin);

        $this->assertEquals('A', $sorted[1]->zone);
        $this->assertEquals('1', $sorted[1]->aisle);
        $this->assertEquals('1', $sorted[1]->rack);
        $this->assertEquals('2', $sorted[1]->bin);

        $this->assertEquals('A', $sorted[2]->zone);
        $this->assertEquals('1', $sorted[2]->aisle);
        $this->assertEquals('2', $sorted[2]->rack);

        $this->assertEquals('A', $sorted[3]->zone);
        $this->assertEquals('2', $sorted[3]->aisle);

        $this->assertEquals('B', $sorted[4]->zone);
    }
}

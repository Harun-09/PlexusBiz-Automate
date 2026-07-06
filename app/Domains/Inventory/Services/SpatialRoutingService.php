<?php

namespace App\Domains\Inventory\Services;

use App\Domains\Inventory\Models\StockLocation;
use Illuminate\Support\Collection;

class SpatialRoutingService
{
    /**
     * Generate an optimized pick path based on Zones, Aisles, Racks, and Bins.
     * Simple sort logic to simulate TSP (Traveling Salesperson) heuristics for a warehouse.
     */
    public function generatePickPath(Collection $locations)
    {
        return $locations->sort(function ($a, $b) {
            // Sort by Zone -> Aisle -> Rack -> Bin
            if ($a->zone !== $b->zone) {
                return $a->zone <=> $b->zone;
            }
            if ($a->aisle !== $b->aisle) {
                return $a->aisle <=> $b->aisle;
            }
            if ($a->rack !== $b->rack) {
                return $a->rack <=> $b->rack;
            }
            return $a->bin <=> $b->bin;
        })->values();
    }
}

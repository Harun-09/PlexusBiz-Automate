<?php

namespace Tests\Feature;

use App\Domains\Tax\Services\VDSEngine;
use Tests\TestCase;

class VDSEngineTest extends TestCase
{
    public function test_vds_engine_calculates_correct_withholding_for_specified_services()
    {
        $engine = new VDSEngine();

        $baseAmount = 1000.00; // e.g. BDT 1000

        // AC Hotel S001.10 (15%)
        $this->assertTrue($engine->requiresVDS('S001.10'));
        $this->assertEquals(150.00, $engine->calculateVDS('S001.10', $baseAmount));

        // Courier S028.00 (15%)
        $this->assertTrue($engine->requiresVDS('S028.00'));
        $this->assertEquals(150.00, $engine->calculateVDS('S028.00', $baseAmount));
    }

    public function test_vds_engine_returns_zero_for_unspecified_services()
    {
        $engine = new VDSEngine();
        
        $baseAmount = 1000.00;

        $this->assertFalse($engine->requiresVDS('RANDOM.SERVICE'));
        $this->assertEquals(0.00, $engine->calculateVDS('RANDOM.SERVICE', $baseAmount));
    }
}

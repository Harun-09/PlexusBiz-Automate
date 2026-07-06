<?php

namespace Tests\Feature;

use App\Domains\Tax\Models\MushakRecord;
use App\Domains\Tax\Services\MushakAccountBookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class MushakAccountBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_monthly_mushak_9_1_return_correctly()
    {
        $service = new MushakAccountBookService();
        $date = Carbon::createFromDate(2026, 7, 15);

        // Purchases (Mushak 6.1)
        MushakRecord::create([
            'book_type' => '6.1',
            'reference_id' => 101,
            'amount' => 1000.00,
            'vat_amount' => 150.00,
            'vds_amount' => 15.00, // We withheld 15 from supplier
            'date' => $date,
        ]);

        // Sales (Mushak 6.2)
        MushakRecord::create([
            'book_type' => '6.2',
            'reference_id' => 201,
            'amount' => 2000.00,
            'vat_amount' => 300.00,
            'vds_amount' => 0.00, 
            'date' => $date,
        ]);

        $return = $service->generateMonthlyReturn(7, 2026);

        $this->assertEquals(150.00, $return['total_input_vat']);
        $this->assertEquals(300.00, $return['total_output_vat']);
        $this->assertEquals(15.00, $return['total_purchase_vds']);
        
        // Output (300) - Input (150) + Purchase VDS (15) = 165
        $this->assertEquals(165.00, $return['net_vat_payable']);
        $this->assertEquals(0, $return['carry_forward']);
    }

    public function test_handles_carry_forward_when_input_vat_exceeds_output()
    {
        $service = new MushakAccountBookService();
        $date = Carbon::createFromDate(2026, 7, 15);

        MushakRecord::create([
            'book_type' => '6.1',
            'reference_id' => 101,
            'amount' => 10000.00,
            'vat_amount' => 1500.00, // High input VAT
            'vds_amount' => 0.00,
            'date' => $date,
        ]);

        MushakRecord::create([
            'book_type' => '6.2',
            'reference_id' => 201,
            'amount' => 2000.00,
            'vat_amount' => 300.00,
            'vds_amount' => 0.00, 
            'date' => $date,
        ]);

        $return = $service->generateMonthlyReturn(7, 2026);

        // Output (300) - Input (1500) = -1200
        $this->assertEquals(0.00, $return['net_vat_payable']);
        $this->assertEquals(1200.00, $return['carry_forward']);
    }
}

<?php

namespace App\Domains\Tax\Services;

use App\Domains\Tax\Models\MushakRecord;

class MushakAccountBookService
{
    /**
     * Get Mushak 6.1 (Purchase Account Book) records for a given month and year.
     */
    public function getPurchaseBook(int $month, int $year)
    {
        return MushakRecord::where('book_type', '6.1')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
    }

    /**
     * Get Mushak 6.2 (Sales Account Book) records for a given month and year.
     */
    public function getSalesBook(int $month, int $year)
    {
        return MushakRecord::where('book_type', '6.2')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
    }

    /**
     * Generate Mushak 9.1 (Monthly VAT Return) summary for a given month and year.
     */
    public function generateMonthlyReturn(int $month, int $year): array
    {
        $purchases = $this->getPurchaseBook($month, $year);
        $sales = $this->getSalesBook($month, $year);

        $totalInputVat = $purchases->sum('vat_amount');
        $totalOutputVat = $sales->sum('vat_amount');
        
        $totalPurchaseVds = $purchases->sum('vds_amount'); // VDS we withheld from suppliers
        $totalSalesVds = $sales->sum('vds_amount'); // VDS customers withheld from us

        // Net VAT Payable = Output VAT - Input VAT (Rebate) + VDS Deducted from Suppliers - VDS Deducted by Customers
        // Actually, VDS deducted from suppliers is payable to Govt.
        // VDS deducted by customers is already paid to Govt, so it reduces our liability.
        $netVatPayable = $totalOutputVat - $totalInputVat + $totalPurchaseVds - $totalSalesVds;

        return [
            'month' => $month,
            'year' => $year,
            'total_input_vat' => $totalInputVat,
            'total_output_vat' => $totalOutputVat,
            'total_purchase_vds' => $totalPurchaseVds,
            'total_sales_vds' => $totalSalesVds,
            'net_vat_payable' => max(0, $netVatPayable), // VAT payable cannot be negative, it rolls over
            'carry_forward' => $netVatPayable < 0 ? abs($netVatPayable) : 0,
        ];
    }
}

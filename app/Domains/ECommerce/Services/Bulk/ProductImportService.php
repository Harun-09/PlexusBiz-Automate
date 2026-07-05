<?php

namespace App\Domains\ECommerce\Services\Bulk;

use App\Domains\ECommerce\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductImportService
{
    /**
     * Import products from CSV
     * @param string $csvFilePath
     */
    public function import(string $csvFilePath): array
    {
        $file = fopen($csvFilePath, 'r');
        if (!$file) {
            return ['status' => 'error', 'message' => 'Unable to open file.'];
        }

        $header = fgetcsv($file);
        $importedCount = 0;
        $failedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                if (count($header) !== count($row)) continue;
                $data = array_combine($header, $row);

                try {
                    Product::updateOrCreate(
                        ['sku' => $data['sku']],
                        [
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                            'base_price' => $data['base_price'],
                            'status' => $data['status'] ?? 'active',
                            'supplier_id' => $data['supplier_id'],
                            'category_id' => $data['category_id'],
                        ]
                    );
                    $importedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to import product SKU {$data['sku']}: " . $e->getMessage());
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        fclose($file);

        return [
            'status' => 'success',
            'imported' => $importedCount,
            'failed' => $failedCount,
        ];
    }
}

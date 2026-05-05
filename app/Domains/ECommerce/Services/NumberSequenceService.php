<?php

namespace App\Domains\ECommerce\Services;

use App\Domains\ECommerce\Models\Invoice;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\Rfq;
use App\Domains\ECommerce\Models\SupplierOrder;
use Illuminate\Support\Str;

class NumberSequenceService
{
    public function orderNumber(): string
    {
        return $this->unique('PO', Order::class, 'order_number');
    }

    public function invoiceNumber(): string
    {
        return $this->unique('INV', Invoice::class, 'invoice_number');
    }

    public function supplierOrderNumber(): string
    {
        return $this->unique('SO', SupplierOrder::class, 'supplier_order_number');
    }

    public function rfqNumber(): string
    {
        return $this->unique('RFQ', Rfq::class, 'rfq_number');
    }

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $model
     */
    private function unique(string $prefix, string $model, string $column): string
    {
        do {
            $number = sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(8)));
        } while ($model::query()->where($column, $number)->exists());

        return $number;
    }
}

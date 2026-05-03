<?php

namespace App\Domains\ECommerce\Enums;

enum InventoryMovementType: string
{
    case StockIn = 'stock_in';
    case StockOut = 'stock_out';
    case Adjustment = 'adjustment';
    case OrderReserve = 'order_reserve';
    case OrderRelease = 'order_release';
}

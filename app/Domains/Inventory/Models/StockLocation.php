<?php

namespace App\Domains\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Assuming we have Warehouse model in ECommerce or Core, let's just reference class path if needed.
    // For now we'll just leave it as integer or relate to App\Domains\Core\Models\Warehouse if it existed,
    // wait, I checked earlier `warehouses` table exists. I'll just keep it simple.
}

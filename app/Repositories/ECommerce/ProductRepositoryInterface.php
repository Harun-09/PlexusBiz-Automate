<?php

namespace App\Repositories\ECommerce;

use App\DTOs\ECommerce\ProductUpsertData;
use App\Domains\ECommerce\Models\Product;
use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface
{
    public function query(): Builder;

    public function create(ProductUpsertData $data): Product;

    public function update(Product $product, ProductUpsertData $data): Product;

    public function delete(Product $product): void;
}


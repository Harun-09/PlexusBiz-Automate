<?php

namespace App\Repositories\ECommerce;

use App\DTOs\ECommerce\ProductUpsertData;
use App\Domains\ECommerce\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function query(): Builder
    {
        return Product::query();
    }

    public function create(ProductUpsertData $data): Product
    {
        return Product::create($data->attributes);
    }

    public function update(Product $product, ProductUpsertData $data): Product
    {
        $product->forceFill($data->attributes)->save();

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}


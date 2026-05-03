<?php

namespace Database\Factories\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\ProductStatus;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(4, true);

        return [
            'supplier_id' => Supplier::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('PX-####-??')),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'description' => fake()->sentence(14),
            'base_price' => fake()->randomFloat(2, 100, 50000),
            'moq' => fake()->numberBetween(1, 20),
            'stock_quantity' => fake()->numberBetween(10, 500),
            'reserved_quantity' => 0,
            'status' => ProductStatus::Active,
            'published_at' => now(),
        ];
    }
}

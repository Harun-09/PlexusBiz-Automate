<?php

namespace Database\Factories\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\SupplierStatus;
use App\Domains\ECommerce\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $company = fake()->company();

        return [
            'user_id' => User::factory(),
            'company_name' => $company,
            'slug' => Str::slug($company).'-'.fake()->unique()->numerify('####'),
            'status' => SupplierStatus::Approved,
            'contact_email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => [
                'line_1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'country' => fake()->country(),
            ],
            'approved_at' => now(),
        ];
    }
}

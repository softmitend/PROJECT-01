<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Cireng', 'Basreng', 'Keripik', 'Seblak']),
            'variant' => $this->faker->randomElement(['Original', 'Pedas', 'Keju', null]),
            'description' => null,
            'default_price' => $this->faker->numberBetween(5000, 30000),
            'is_active' => true,
        ];
    }
}

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
            'name' => $this->faker->randomElement(['K-pop Album', 'Official Photocard', 'Lightstick', 'Season Greetings', 'Plush Doll']),
            'variant' => $this->faker->randomElement(['Weverse POB', 'Random Member', 'Official MD', 'Limited Edition', null]),
            'description' => $this->faker->sentence(),
            'default_price' => $this->faker->numberBetween(75000, 1200000),
            'is_active' => true,
        ];
    }
}

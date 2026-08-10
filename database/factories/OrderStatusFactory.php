<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderStatus>
 */
class OrderStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->slug(2),
            'description' => null,
            'color' => $this->faker->hexColor(),
            'sequence' => $this->faker->numberBetween(1, 20),
            'status_type' => 'process',
            'scope' => 'all',
            'is_initial' => false,
            'is_final' => false,
            'is_active' => true,
        ];
    }
}

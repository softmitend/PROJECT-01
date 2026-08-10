<?php

namespace Database\Factories;

use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->numberBetween(75000, 1200000);

        return [
            'member_order_id' => MemberOrder::factory(),
            'product_id' => Product::factory(),
            'item_name' => $this->faker->randomElement(['K-pop Album', 'Official Photocard', 'Lightstick']),
            'variant' => $this->faker->randomElement(['Weverse POB', 'Random Member', 'Official MD', null]),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
            'override_status_id' => null,
            'notes' => null,
        ];
    }
}

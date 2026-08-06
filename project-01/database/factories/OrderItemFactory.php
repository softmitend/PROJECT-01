<?php

namespace Database\Factories;

use App\Models\MemberOrder;
use App\Models\OrderItem;
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
        $unitPrice = $this->faker->numberBetween(5000, 25000);

        return [
            'member_order_id' => MemberOrder::factory(),
            'product_id' => null,
            'item_name' => $this->faker->randomElement(['Cireng', 'Basreng', 'Keripik']),
            'variant' => $this->faker->randomElement(['Original', 'Pedas', null]),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
            'override_status_id' => null,
            'notes' => null,
        ];
    }
}

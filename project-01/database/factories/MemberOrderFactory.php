<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Member;
use App\Models\MemberOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MemberOrder>
 */
class MemberOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_code' => 'ORD-'.$this->faker->unique()->numerify('######'),
            'member_id' => Member::factory(),
            'batch_id' => Batch::factory(),
            'override_status_id' => null,
            'payment_status_id' => null,
            'total_amount' => null,
            'payment_status' => null,
            'notes' => null,
        ];
    }
}

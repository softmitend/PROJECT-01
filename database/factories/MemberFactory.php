<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_code' => 'MBR-'.$this->faker->unique()->numerify('####'),
            'display_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('08##########'),
            'address' => $this->faker->address(),
            'notes' => null,
            'is_active' => true,
        ];
    }
}

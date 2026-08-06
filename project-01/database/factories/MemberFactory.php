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
            'username' => $this->faker->unique()->userName(),
            'access_code' => $this->faker->bothify('AC-####'),
            'group_name' => $this->faker->randomElement(['A-E', 'F-J', 'K-O', 'P-T', 'U-Z']),
            'notes' => null,
            'is_active' => true,
        ];
    }
}

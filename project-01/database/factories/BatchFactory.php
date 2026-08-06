<?php

namespace Database\Factories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_number' => (string) $this->faker->unique()->numberBetween(1000, 9999),
            'batch_name' => 'Batch '.$this->faker->word(),
            'current_status_id' => null,
            'description' => null,
            'notes' => null,
            'started_at' => now(),
            'completed_at' => null,
            'is_archived' => false,
        ];
    }
}

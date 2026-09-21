<?php

namespace Database\Factories;

use App\Models\Thesis;
use App\Models\ThesisLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThesisLog>
 */
class ThesisLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'thesis_id' => Thesis::factory(),
            'date' => fake()->date(),
            'activity' => fake()->sentence(3),
            'notes' => fake()->optional()->paragraph(),
            'supervisor_approval' => fake()->boolean(),
        ];
    }
}

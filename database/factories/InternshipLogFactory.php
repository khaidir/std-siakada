<?php

namespace Database\Factories;

use App\Enums\InternshipLogApproval;
use App\Models\Internship;
use App\Models\InternshipLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InternshipLog>
 */
class InternshipLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'internship_id' => Internship::factory(),
            'date' => fake()->date(),
            'activity' => fake()->sentence(3),
            'notes' => fake()->optional()->paragraph(),
            'approval' => fake()->randomElement(InternshipLogApproval::cases()),
        ];
    }
}

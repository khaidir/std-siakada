<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'max_score' => 100,
        ];
    }
}

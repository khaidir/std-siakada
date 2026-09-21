<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentSubmission>
 */
class AssignmentSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => Student::factory(),
            'file_path' => '/uploads/'.fake()->uuid().'.pdf',
            'submitted_at' => fake()->optional()->dateTimeBetween('-2 weeks', 'now'),
            'score' => fake()->optional()->randomFloat(2, 0, 100),
            'feedback' => fake()->optional()->sentence(),
        ];
    }
}

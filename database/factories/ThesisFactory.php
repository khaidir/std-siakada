<?php

namespace Database\Factories;

use App\Enums\ThesisStatus;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\Thesis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thesis>
 */
class ThesisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'title' => fake()->sentence(6),
            'abstract' => fake()->paragraph(),
            'supervisor_1_id' => Lecturer::factory(),
            'supervisor_2_id' => Lecturer::factory(),
            'status' => fake()->randomElement(ThesisStatus::cases()),
            'submission_date' => fake()->date(),
        ];
    }
}

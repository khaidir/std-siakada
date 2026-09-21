<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nim' => fake()->unique()->numerify('##########'),
            'study_program_id' => StudyProgram::factory(),
            'entry_year' => (string) fake()->numberBetween(2019, 2025),
            'status' => fake()->randomElement(StudentStatus::cases()),
            'gpa' => fake()->randomFloat(2, 0, 4),
            'total_sks' => fake()->numberBetween(0, 144),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\CourseType;
use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'study_program_id' => StudyProgram::factory(),
            'code' => strtoupper(fake()->unique()->bothify('??###')),
            'name' => 'Mata Kuliah '.ucfirst(fake()->unique()->words(2, true)),
            'sks' => fake()->randomElement([2, 3, 4]),
            'semester' => fake()->numberBetween(1, 8),
            'type' => fake()->randomElement(CourseType::cases()),
        ];
    }
}

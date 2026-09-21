<?php

namespace Database\Factories;

use App\Enums\DegreeLevel;
use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyProgram>
 */
class StudyProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'code' => strtoupper(fake()->unique()->lexify('??')),
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'degree_level' => fake()->randomElement(DegreeLevel::cases()),
        ];
    }
}

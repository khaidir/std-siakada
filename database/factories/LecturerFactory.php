<?php

namespace Database\Factories;

use App\Enums\AcademicRank;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lecturer>
 */
class LecturerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nidn' => fake()->unique()->numerify('##########'),
            'study_program_id' => StudyProgram::factory(),
            'academic_rank' => fake()->randomElement(AcademicRank::cases()),
        ];
    }
}

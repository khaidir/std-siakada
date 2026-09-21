<?php

namespace Database\Factories;

use App\Enums\SemesterType;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Semester>
 */
class SemesterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'type' => fake()->randomElement(SemesterType::cases()),
            'start_date' => '2025-08-01',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\StudyPlanStatus;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyPlan>
 */
class StudyPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'semester_id' => Semester::factory(),
            'status' => fake()->randomElement(StudyPlanStatus::cases()),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
}

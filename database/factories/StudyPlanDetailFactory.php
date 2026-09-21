<?php

namespace Database\Factories;

use App\Enums\StudyPlanDetailStatus;
use App\Models\CourseOffering;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyPlanDetail>
 */
class StudyPlanDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'study_plan_id' => StudyPlan::factory(),
            'course_offering_id' => CourseOffering::factory(),
            'status' => fake()->randomElement(StudyPlanDetailStatus::cases()),
        ];
    }
}

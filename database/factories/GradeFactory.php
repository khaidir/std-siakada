<?php

namespace Database\Factories;

use App\Enums\GradeLetter;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudyPlanDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        $letter = fake()->randomElement(GradeLetter::cases());

        return [
            'study_plan_detail_id' => StudyPlanDetail::factory(),
            'student_id' => Student::factory(),
            'course_offering_id' => CourseOffering::factory(),
            'assignment_score' => fake()->randomFloat(2, 0, 100),
            'midterm_score' => fake()->randomFloat(2, 0, 100),
            'final_score' => fake()->randomFloat(2, 0, 100),
            'score' => fake()->randomFloat(2, 0, 100),
            'letter_grade' => $letter,
            'grade_point' => $letter->point(),
        ];
    }
}

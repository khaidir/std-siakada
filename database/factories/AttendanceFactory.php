<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\CourseOffering;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'student_id' => Student::factory(),
            'meeting_number' => fake()->numberBetween(1, 16),
            'date' => fake()->date(),
            'status' => fake()->randomElement(AttendanceStatus::cases()),
        ];
    }
}

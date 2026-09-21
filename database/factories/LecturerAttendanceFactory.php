<?php

namespace Database\Factories;

use App\Enums\LecturerAttendanceStatus;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\LecturerAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LecturerAttendance>
 */
class LecturerAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lecturer_id' => Lecturer::factory(),
            'course_offering_id' => CourseOffering::factory(),
            'date' => fake()->date(),
            'check_in' => '08:00:00',
            'check_out' => '10:00:00',
            'status' => fake()->randomElement(LecturerAttendanceStatus::cases()),
        ];
    }
}

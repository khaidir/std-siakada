<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseOffering>
 */
class CourseOfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'semester_id' => Semester::factory(),
            'lecturer_id' => Lecturer::factory(),
            'classroom_id' => Classroom::factory(),
            'day' => fake()->randomElement(DayOfWeek::cases()),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'quota' => fake()->numberBetween(20, 50),
        ];
    }
}

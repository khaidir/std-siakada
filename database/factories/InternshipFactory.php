<?php

namespace Database\Factories;

use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Internship>
 */
class InternshipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'company_name' => fake()->company(),
            'address' => fake()->address(),
            'supervisor_id' => Lecturer::factory(),
            'field_supervisor' => fake()->name(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => fake()->randomElement(InternshipStatus::cases()),
        ];
    }
}

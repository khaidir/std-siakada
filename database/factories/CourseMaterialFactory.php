<?php

namespace Database\Factories;

use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseMaterial>
 */
class CourseMaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'file_path' => fake()->optional(0.3)->regexify('/uploads/[a-f0-9]{8}\.pdf'),
            'uploaded_by' => User::factory(),
        ];
    }
}

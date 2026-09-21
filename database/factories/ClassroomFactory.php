<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(1, 999);

        return [
            'code' => 'R'.$number,
            'name' => 'Ruang '.$number,
            'capacity' => fake()->numberBetween(20, 50),
            'building' => fake()->optional()->randomElement(['A', 'B', 'C', 'D']),
        ];
    }
}

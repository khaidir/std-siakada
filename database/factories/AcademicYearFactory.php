<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2015, 2035);

        return [
            'code' => (string) $year,
            'name' => $year.'/'.($year + 1),
            'start_date' => $year.'-08-01',
            'end_date' => ($year + 1).'-07-31',
            'is_active' => false,
        ];
    }
}

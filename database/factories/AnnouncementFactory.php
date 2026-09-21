<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'content' => fake()->paragraph(),
            'target_role' => fake()->optional()->randomElement([
                'super-admin', 'kaprodi', 'dosen', 'mahasiswa', 'pimpinan',
            ]),
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}

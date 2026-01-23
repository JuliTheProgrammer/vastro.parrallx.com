<?php

namespace Database\Factories;

use App\Models\Backup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'linkable_type' => Backup::class,
            'linkable_id' => Backup::factory(),
            'name' => fake()->words(2, true),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+1 year')
                ?->format('Y-m-d H:i:s'),
        ];
    }
}

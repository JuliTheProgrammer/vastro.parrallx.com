<?php

namespace Database\Factories;

use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backup>
 */
class BackupFactory extends Factory
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
            'backupable_type' => Vault::class,
            'backupable_id' => Vault::factory(),
            'name' => fake()->words(3, true),
            'path' => fake()->filePath(),
            'mime_type' => fake()->mimeType(),
            'size_megaBytes' => fake()->numberBetween(1, 512),
        ];
    }
}

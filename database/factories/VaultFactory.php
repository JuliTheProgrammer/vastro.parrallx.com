<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vault>
 */
class VaultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'uuid' => fake()->uuid(),
            'user_id' => User::factory(),
            'aws_bucket_arn' => fake()->uuid(),
            'aws_bucket_name' => fake()->name(),
            'region' => fake()->name(),
            'location' => fake()->city(),
            'worm_protection' => fake()->boolean(),
            'versioning' => fake()->boolean(),
            'delete_protection' => fake()->boolean(),
        ];
    }
}

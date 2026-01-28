<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->ean8(),
            'name' => $this->faker->city(),
            'AZs' => $this->faker->numberBetween(1, 3),
            'geography' => $this->faker->country(),
            'active' => $this->faker->boolean(),
        ];
    }
}

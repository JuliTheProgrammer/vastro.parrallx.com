<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
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
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'amount' => fake()->numberBetween(900, 25000),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP']),
            'status' => fake()->boolean(80),
            'date' => fake()->dateTimeBetween('-12 months', 'now'),
        ];
    }
}

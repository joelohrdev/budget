<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->randomElement(['Rent', 'Electricity', 'Water', 'Internet', 'Phone', 'Car Payment', 'Insurance']),
            'amount' => fake()->randomFloat(2, 50, 2000),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}

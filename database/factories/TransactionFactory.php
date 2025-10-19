<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_id' => \App\Models\Card::factory(),
            'description' => fake()->randomElement([
                'Grocery Store',
                'Gas Station',
                'Restaurant',
                'Coffee Shop',
                'Online Shopping',
                'Utility Bill',
                'Subscription Service',
            ]),
            'amount' => fake()->randomFloat(2, 5, 500),
            'type' => 'debit',
            'transaction_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit',
            'description' => 'Credit/Refund',
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['debit', 'credit']);

        return [
            'user_id' => \App\Models\User::factory(),
            'pay_period_id' => \App\Models\PayPeriod::factory(),
            'name' => fake()->randomElement(['Chase', 'Wells Fargo', 'Bank of America', 'Citi', 'Capital One']),
            'type' => $type,
            'budget_limit' => fake()->randomFloat(2, 500, 5000),
        ];
    }

    public function debit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'debit',
        ]);
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit',
        ]);
    }
}

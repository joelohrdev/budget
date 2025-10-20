<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['credit_card', 'loan', 'mortgage', 'other']);
        $principalAmount = match ($type) {
            'credit_card' => fake()->randomFloat(2, 1000, 15000),
            'loan' => fake()->randomFloat(2, 5000, 50000),
            'mortgage' => fake()->randomFloat(2, 100000, 500000),
            'other' => fake()->randomFloat(2, 1000, 25000),
        };
        $currentBalance = fake()->randomFloat(2, $principalAmount * 0.3, $principalAmount);
        $interestRate = match ($type) {
            'credit_card' => fake()->randomFloat(2, 15, 25),
            'loan' => fake()->randomFloat(2, 5, 12),
            'mortgage' => fake()->randomFloat(2, 3, 7),
            'other' => fake()->randomFloat(2, 5, 15),
        };

        return [
            'user_id' => \App\Models\User::factory(),
            'name' => match ($type) {
                'credit_card' => fake()->randomElement(['Chase Freedom', 'Discover Card', 'Capital One', 'AmEx Blue']),
                'loan' => fake()->randomElement(['Auto Loan', 'Personal Loan', 'Student Loan']),
                'mortgage' => fake()->randomElement(['Home Mortgage', 'Second Mortgage']),
                'other' => fake()->randomElement(['Medical Debt', 'Family Loan']),
            },
            'type' => $type,
            'principal_amount' => $principalAmount,
            'current_balance' => $currentBalance,
            'interest_rate' => $interestRate,
            'minimum_payment' => fake()->randomFloat(2, $currentBalance * 0.01, $currentBalance * 0.05),
            'term_months' => $type === 'credit_card' ? null : fake()->numberBetween(12, 360),
            'start_date' => fake()->dateTimeBetween('-2 years', '-6 months'),
            'payoff_target_date' => fake()->optional()->dateTimeBetween('+6 months', '+5 years'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

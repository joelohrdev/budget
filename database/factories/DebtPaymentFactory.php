<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtPayment>
 */
class DebtPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 50, 1000);
        $interestAmount = fake()->randomFloat(2, 10, $amount * 0.5);
        $principalAmount = $amount - $interestAmount;

        return [
            'debt_id' => \App\Models\Debt::factory(),
            'amount' => $amount,
            'principal_amount' => $principalAmount,
            'interest_amount' => $interestAmount,
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

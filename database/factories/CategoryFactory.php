<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Groceries' => '#10b981',
            'Dining' => '#f59e0b',
            'Transportation' => '#3b82f6',
            'Entertainment' => '#8b5cf6',
            'Shopping' => '#ec4899',
            'Utilities' => '#6366f1',
            'Healthcare' => '#14b8a6',
            'Other' => '#6b7280',
        ];

        $category = fake()->randomElement(array_keys($categories));

        return [
            'name' => $category,
            'color' => $categories[$category],
        ];
    }
}

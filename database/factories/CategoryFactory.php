<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    // Defines default fake data for categories.
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Software Engineering',
                'Product Management',
                'Data Science',
                'Cybersecurity',
                'DevOps',
                'Mobile Development',
                'Cloud Architecture',
                'QA Automation',
                'UI UX Design',
                'Technical Support',
            ]),
        ];
    }
}

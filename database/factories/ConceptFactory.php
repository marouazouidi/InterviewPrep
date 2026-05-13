<?php

namespace Database\Factories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConceptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'explanation' => fake()->paragraphs(3, true),
            'difficulty' => fake()->randomElement(['junior', 'mid', 'senior']),
            'status' => 'to_review',
            'domain_id' => Domain::inRandomOrder()->first()->id,
        ];
    }
}

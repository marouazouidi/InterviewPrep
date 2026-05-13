<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(5),
            'color' => fake()->randomElement([
                'bg-blue-500',
                'bg-green-500',
                'bg-red-500',
                'bg-orange-500',
                'bg-purple-500',
                'bg-pink-500',
            ]),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}

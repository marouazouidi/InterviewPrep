<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'admin', 'password' => bcrypt('admin123')]
        );

        Domain::factory()->create([
            'name' => 'Laravel ORM',
            'color' => 'bg-blue-500',
            'user_id' => $user->id,
        ]);

        Domain::factory()->create([
            'name' => 'PHP OOP',
            'color' => 'bg-green-500',
            'user_id' => $user->id,
        ]);
    }
}

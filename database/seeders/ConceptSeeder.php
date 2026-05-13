<?php

namespace Database\Seeders;

use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Database\Seeder;

class ConceptSeeder extends Seeder
{
    public function run(): void
    {
        $domains = Domain::all();

        foreach ($domains as $domain) {
            Concept::factory()->create([
                'title' => 'Eloquent N+1 Problem',
                'explanation' => fake()->paragraphs(3, true),
                'difficulty' => 'mid',
                'domain_id' => $domain->id,
            ]);

            Concept::factory()->create([
                'title' => 'Polymorphic Relations',
                'explanation' => fake()->paragraphs(3, true),
                'difficulty' => 'senior',
                'domain_id' => $domain->id,
            ]);

            Concept::factory()->create([
                'title' => 'Laravel Queues',
                'explanation' => fake()->paragraphs(3, true),
                'difficulty' => 'junior',
                'domain_id' => $domain->id,
            ]);
        }
    }
}

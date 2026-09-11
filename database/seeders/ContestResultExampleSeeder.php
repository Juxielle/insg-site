<?php

namespace Database\Seeders;

use App\Models\Contest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContestResultExampleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $contest = Contest::firstOrCreate(['reference' => 'DEMO-CONC-2026'], [
                'title' => 'Concours de démonstration — Données fictives', 'description' => 'Exemple de consultation des résultats.',
                'academic_year' => '2026-2027', 'session' => 'Septembre 2026', 'type' => 'Démonstration',
                'registration_starts_at' => '2026-08-01', 'registration_ends_at' => '2026-08-31',
                'exam_date' => '2026-09-01', 'exam_time' => '08:00', 'location' => 'INSG', 'available_places' => 1,
                'status' => 'results_published', 'published_at' => now(),
            ]);
            $contest->rounds()->firstOrCreate(['number' => 1], ['published_at' => now()]);
            $contest->rounds()->firstOrCreate(['number' => 2]);
            $contest->entries()->firstOrCreate(['round' => 1, 'registration_number' => 'TEST-INSG-2026-001'], [
                'last_name' => 'EXEMPLE', 'first_names' => 'Camille', 'birth_date' => '2005-04-12',
                'field' => 'Licence en science de gestion', 'average' => 14, 'rank' => 1, 'decision' => 'Admis(e)',
            ]);
        });
    }
}

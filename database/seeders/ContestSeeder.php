<?php

namespace Database\Seeders;

use App\Models\Contest;
use Illuminate\Database\Seeder;

class ContestSeeder extends Seeder
{
    public function run(): void
    {
        Contest::updateOrCreate(['reference' => 'CONC-2026-001'], [
            'title' => 'Concours d’entrée INSG 2026-2027', 'description' => 'Concours officiel d’admission aux formations de l’Institut National des Sciences de Gestion.',
            'academic_year' => '2026-2027', 'session' => 'Septembre 2026', 'type' => 'Concours d’entrée',
            'registration_starts_at' => '2026-08-01 00:00:00', 'registration_ends_at' => '2026-09-20 23:59:00',
            'exam_date' => '2026-09-30', 'exam_time' => '08:00', 'location' => 'Campus INSG, Libreville', 'available_places' => 200,
            'status' => 'registration_open', 'additional_information' => 'Se présenter 30 minutes avant l’épreuve avec une pièce d’identité et la convocation.',
        ]);
    }
}

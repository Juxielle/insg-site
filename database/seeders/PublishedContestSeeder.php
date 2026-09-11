<?php

namespace Database\Seeders;

use App\Models\Contest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublishedContestSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::where('role', 'admin')->first();
            $contest = Contest::updateOrCreate(['reference' => 'CONC-2026-002'], [
                'title' => 'Concours d’entrée INSG — Session de juin 2026',
                'description' => 'Résultats officiels du concours d’entrée aux programmes de Licence de l’INSG.',
                'academic_year' => '2026-2027',
                'session' => 'Juin 2026',
                'type' => 'Concours d’entrée en Licence',
                'registration_starts_at' => '2026-03-02 08:00:00',
                'registration_ends_at' => '2026-09-20 23:59:00',
                'exam_date' => '2026-06-06',
                'exam_time' => '08:00',
                'location' => 'Campus INSG, Libreville',
                'available_places' => 150,
                'status' => 'results_published',
                'additional_information' => 'Résultats validés par la Direction Générale de l’INSG.',
                'results_validated_at' => '2026-06-18 10:00:00',
                'published_at' => '2026-06-20 09:00:00',
                'published_by' => $admin?->id,
                'closed_at' => '2026-05-15 18:00:00',
            ]);

            $candidates = [
                ['0001', 'OBIANG', 'Grâce Mireille', 'F', '2005-02-14', 'Libreville', 'grace.obiang@example.test', 'Licence fondamentale', [16, 15, 14, 18]],
                ['0002', 'MBA', 'Jean-Paul', 'M', '2004-11-03', 'Oyem', 'jean.mba@example.test', 'BTS', [19, 18, 17, 20]],
                ['0003', 'NDONG', 'Alice', 'F', '2005-07-21', 'Port-Gentil', 'alice.ndong@example.test', 'Licence professionnelle', [13, 12, 11, 15]],
                ['0004', 'MOUNDOUNGA', 'Eric', 'M', '2004-09-12', 'Franceville', 'eric.moundounga@example.test', 'BTS', [9, 8, 7, 10]],
                ['0005', 'MBOUMBA', 'Sarah', 'F', '2005-04-08', 'Lambaréné', 'sarah.mboumba@example.test', 'Licence fondamentale', [17, 16, 15, 16]],
            ];

            foreach ([1, 2] as $number) $contest->rounds()->updateOrCreate(['number' => $number], ['published_at' => $number === 1 ? $contest->published_at : null]);
            foreach ($candidates as $index => [$number, $lastName, $firstNames, $gender, $birthDate, $birthPlace, $email, $trackName, $scores]) {
                $average = array_sum($scores) / count($scores);
                $contest->entries()->updateOrCreate(['round' => 1, 'last_name' => $lastName, 'first_names' => $firstNames], [
                    'birth_date' => $birthDate, 'registration_number' => 'INSG-2026-'.$number, 'field' => $trackName, 'average' => $average, 'decision' => $average >= 10 ? 'Admis(e)' : 'Ajourné(e)', 'rank' => [3, 1, 4, 5, 2][$index],
                ]);
            }
        });
    }
}

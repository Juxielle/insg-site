<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Contest;
use App\Models\ContestApplication;
use App\Models\ContestResult;
use App\Models\User;
use App\Services\ContestService;
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
                'registration_ends_at' => '2026-05-15 18:00:00',
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
                ['0001', 'OBIANG', 'Grâce Mireille', 'F', '2005-02-14', 'Libreville', 'grace.obiang@example.test', '15.75'],
                ['0002', 'MBA', 'Jean-Paul', 'M', '2004-11-03', 'Oyem', 'jean.mba@example.test', '18.50'],
                ['0003', 'NDONG', 'Alice', 'F', '2005-07-21', 'Port-Gentil', 'alice.ndong@example.test', '12.75'],
                ['0004', 'MOUNDOUNGA', 'Eric', 'M', '2004-09-12', 'Franceville', 'eric.moundounga@example.test', '8.50'],
                ['0005', 'MBOUMBA', 'Sarah', 'F', '2005-04-08', 'Lambaréné', 'sarah.mboumba@example.test', '16.00'],
            ];

            $service = app(ContestService::class);
            foreach ($candidates as $index => [$number, $lastName, $firstNames, $gender, $birthDate, $birthPlace, $email, $average]) {
                $candidate = Candidate::firstOrNew(['email' => $email]);
                $candidate->fill([
                    'registration_number' => 'INSG-2026-'.$number,
                    'last_name' => $lastName, 'first_names' => $firstNames, 'gender' => $gender,
                    'birth_date' => $birthDate, 'birth_place' => $birthPlace, 'nationality' => 'Gabonaise',
                    'phone' => '+241 06 00 0'.$number, 'address' => 'Libreville', 'city' => 'Libreville', 'province' => 'Estuaire',
                    'study_level' => 'Terminale', 'previous_school' => 'Lycée national', 'diploma' => 'Baccalauréat',
                    'graduation_year' => 2026, 'field' => 'Série B', 'specialty' => 'Économie',
                ])->save();
                $application = ContestApplication::updateOrCreate(
                    ['contest_id' => $contest->id, 'candidate_id' => $candidate->id],
                    ['candidate_number' => 'CONC-2026-002-'.sprintf('%04d', $index + 1), 'status' => 'validated', 'source' => 'public',
                     'verification_code' => substr(hash('sha256', 'insg-demo-'.$number), 0, 32), 'submitted_at' => '2026-04-10 10:00:00',
                     'reviewed_at' => '2026-05-20 09:00:00', 'reviewed_by' => $admin?->id]
                );
                ContestResult::updateOrCreate(['contest_application_id' => $application->id], [
                    'average' => $average, 'mention' => $service->mention((float) $average),
                    'decision' => $service->decision((float) $average),
                ]);
            }

            $results = ContestResult::whereHas('application', fn ($query) => $query->where('contest_id', $contest->id))
                ->orderByDesc('average')->orderBy('id')->get();
            $previous = null; $rank = 0;
            foreach ($results as $index => $result) {
                if ($previous === null || (float) $result->average !== $previous) $rank = $index + 1;
                $result->update(['rank' => $rank]);
                $previous = (float) $result->average;
            }
        });
    }
}

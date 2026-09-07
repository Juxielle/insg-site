<?php

namespace App\Services;

use App\Models\Contest;
use App\Models\ContestScore;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Shuchkin\SimpleXLSX;

class ContestExcelImportService
{
    public function __construct(private ContestService $contests) {}

    public function import(Contest $contest, UploadedFile $file, User $user): array
    {
        $xlsx = SimpleXLSX::parse($file->getRealPath());
        if (! $xlsx) throw ValidationException::withMessages(['results_file' => 'Le fichier Excel est illisible : '.SimpleXLSX::parseError()]);

        $contest->load('tracks.subjects');
        $imported = 0; $errors = [];
        foreach ($xlsx->sheetNames() as $sheetIndex => $sheetName) {
            $track = $contest->tracks->first(fn ($item) => $this->key($item->name) === $this->key($sheetName));
            if (! $track) { $errors[] = "Feuille « {$sheetName} » ignorée : aucune filière correspondante."; continue; }
            $rows = $xlsx->rows($sheetIndex);
            $headers = array_map(fn ($value) => $this->key((string) $value), array_shift($rows) ?? []);
            $columns = array_flip($headers);
            foreach (['nom', 'prenoms', 'email'] as $required) {
                if (! isset($columns[$required])) { $errors[] = "Feuille « {$sheetName} » : colonne {$required} manquante."; continue 2; }
            }
            foreach ($track->subjects as $subject) {
                if (! isset($columns[$this->key($subject->name)])) { $errors[] = "Feuille « {$sheetName} » : matière {$subject->name} manquante."; continue 2; }
            }

            foreach ($rows as $offset => $row) {
                if (collect($row)->filter(fn ($value) => $value !== null && $value !== '')->isEmpty()) continue;
                $line = $offset + 2;
                try {
                    $email = trim((string) ($row[$columns['email']] ?? ''));
                    $lastName = trim((string) ($row[$columns['nom']] ?? ''));
                    $firstNames = trim((string) ($row[$columns['prenoms']] ?? ''));
                    if (! $email || ! $lastName || ! $firstNames) throw ValidationException::withMessages(['row' => 'nom, prenoms et email sont obligatoires']);
                    $validatedScores = [];
                    foreach ($track->subjects as $subject) {
                        $score = $row[$columns[$this->key($subject->name)]] ?? null;
                        if (! is_numeric($score) || (float) $score < 0 || (float) $score > (float) $subject->max_score) {
                            throw ValidationException::withMessages(['score' => "{$subject->name} doit être comprise entre 0 et {$subject->max_score}"]);
                        }
                        $validatedScores[$subject->id] = (float) $score;
                    }
                    $birthDate = $this->value($row, $columns, 'date_naissance') ?: '2000-01-01';
                    $application = $this->contests->submitApplication($contest, [
                        'last_name' => $lastName, 'first_names' => $firstNames,
                        'gender' => $this->value($row, $columns, 'sexe'), 'birth_date' => $birthDate,
                        'birth_place' => $this->value($row, $columns, 'lieu_naissance') ?: 'Non renseigné',
                        'nationality' => $this->value($row, $columns, 'nationalite') ?: 'Gabonaise',
                        'phone' => $this->value($row, $columns, 'telephone') ?: 'Non renseigné', 'email' => $email,
                        'city' => $this->value($row, $columns, 'ville') ?: 'Libreville',
                        'study_level' => $this->value($row, $columns, 'niveau') ?: $track->name,
                        'diploma' => $this->value($row, $columns, 'diplome') ?: 'Non renseigné',
                    ], [], 'import', $user, $track->id);
                    foreach ($validatedScores as $subjectId => $score) ContestScore::create(['contest_application_id' => $application->id, 'contest_subject_id' => $subjectId, 'score' => $score]);
                    $this->contests->calculateApplicationResult($application->fresh(['track.subjects', 'scores']));
                    $imported++;
                } catch (\Throwable $exception) {
                    $errors[] = "Feuille « {$sheetName} », ligne {$line} : ".($exception instanceof ValidationException ? collect($exception->errors())->flatten()->first() : $exception->getMessage());
                }
            }
        }
        if ($imported) $contest->update(['status' => 'results_preparation']);
        return compact('imported', 'errors');
    }

    private function key(string $value): string { return Str::of($value)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString(); }
    private function value(array $row, array $columns, string $key): ?string { return isset($columns[$key]) ? trim((string) ($row[$columns[$key]] ?? '')) ?: null : null; }
}

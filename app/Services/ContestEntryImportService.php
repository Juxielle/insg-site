<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Shuchkin\SimpleXLSX;

class ContestEntryImportService
{
    public static function rules(): array
    {
        return [
            'registration_number' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_names' => ['required', 'string', 'max:150'],
            'field' => ['required', 'string', 'max:150'],
            'average' => ['required', 'numeric', 'between:0,20'],
            'decision' => ['required', \Illuminate\Validation\Rule::in(['Admis(e)', 'Ajourné(e)'])],
            'rank' => ['required', 'integer', 'min:1', 'max:2147483647'],
        ];
    }

    public function read(UploadedFile $file): array
    {
        $xlsx = SimpleXLSX::parse($file->getRealPath());
        if (! $xlsx) throw ValidationException::withMessages(['excel_file' => 'Le fichier Excel est illisible. Utilisez un fichier .xlsx valide.']);
        $aliases = ['date de naissance' => 'birth_date', 'date_naissance' => 'birth_date', 'matricule' => 'registration_number', 'nom' => 'last_name', 'prenom' => 'first_names', 'prenoms' => 'first_names', 'filiere' => 'field', 'moyenne' => 'average', 'decision' => 'decision', 'rang' => 'rank'];
        $columns = []; $entries = []; $errors = [];
        foreach ($xlsx->readRows() as $index => $row) {
            if ($index === 0) {
                foreach ($row as $column => $label) {
                    $key = $aliases[Str::lower(Str::ascii(trim((string) $label)))] ?? null;
                    if ($key && isset($columns[$key])) throw ValidationException::withMessages(['excel_file' => 'Une colonne obligatoire apparaît plusieurs fois.']);
                    if ($key) $columns[$key] = $column;
                }
                if (array_diff(['last_name', 'first_names', 'field', 'average', 'decision', 'rank'], array_keys($columns))) throw ValidationException::withMessages(['excel_file' => 'La première ligne doit contenir : nom, prénom, filière, moyenne, décision, rang. La colonne matricule est facultative.']);
                continue;
            }
            if (collect($row)->every(fn ($value) => trim((string) $value) === '')) continue;
            if ($index > 5000) throw ValidationException::withMessages(['excel_file' => 'Le fichier ne doit pas dépasser 5 000 lignes.']);
            $entry = [];
            foreach ($columns as $key => $column) $entry[$key] = trim((string) ($row[$column] ?? ''));
            $entry['average'] = str_replace(',', '.', $entry['average']);
            $entry['decision'] = match (Str::lower(Str::ascii($entry['decision']))) {
                'admis', 'admise', 'admis(e)', 'admitted' => 'Admis(e)',
                'ajourne', 'ajournee', 'ajourne(e)', 'non admis', 'not_admitted' => 'Ajourné(e)',
                default => $entry['decision'],
            };
            if (! empty($entry['birth_date'])) {
                $date = $entry['birth_date'];
                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $parts)) $date = $parts[3].'-'.$parts[2].'-'.$parts[1];
                elseif (preg_match('/^\d{4}-\d{2}-\d{2} 00:00:00$/', $date)) $date = substr($date, 0, 10);
                $entry['birth_date'] = $date;
            } else $entry['birth_date'] = null;
            $validator = Validator::make($entry, self::rules(), [], [
                'birth_date' => 'date de naissance', 'registration_number' => 'matricule', 'last_name' => 'nom', 'first_names' => 'prénom', 'field' => 'filière', 'average' => 'moyenne', 'decision' => 'décision', 'rank' => 'rang',
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) $errors[] = 'Ligne '.($index + 1).' : '.$error;
                if (count($errors) >= 20) break;
            } else {
                $entries[] = $validator->validated();
            }
        }
        if ($errors) throw ValidationException::withMessages(['excel_file' => $errors]);
        if (! $entries) throw ValidationException::withMessages(['excel_file' => 'Le fichier ne contient aucun étudiant.']);
        return $entries;
    }
}

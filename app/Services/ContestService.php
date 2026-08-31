<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Contest;
use App\Models\ContestApplication;
use App\Models\ContestAudit;
use App\Models\ContestResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ContestService
{
    public const TRANSITIONS = [
        'draft' => ['registration_open', 'archived'],
        'registration_open' => ['registration_closed', 'draft'],
        'registration_closed' => ['completed', 'registration_open'],
        'completed' => ['results_preparation'],
        'results_preparation' => ['results_published', 'completed'],
        'results_published' => ['results_preparation', 'archived'],
        'archived' => [],
    ];

    public function createContest(array $data, User $user): Contest
    {
        return DB::transaction(function () use ($data, $user): Contest {
            $year = (int) substr($data['academic_year'], 0, 4);
            $sequence = Contest::where('reference', 'like', "CONC-{$year}-%")->lockForUpdate()->count() + 1;
            $contest = Contest::create($data + ['reference' => sprintf('CONC-%d-%03d', $year, $sequence), 'status' => 'draft']);
            $this->audit($user, 'contest.created', $contest);
            return $contest;
        });
    }

    public function transition(Contest $contest, string $status, User $user): void
    {
        if (! in_array($status, self::TRANSITIONS[$contest->status] ?? [], true)) {
            throw ValidationException::withMessages(['status' => 'Cette transition de statut n’est pas autorisée.']);
        }
        if ($status === 'results_published') {
            $this->publish($contest, $user);
            return;
        }
        $contest->update(['status' => $status, 'closed_at' => $status === 'registration_closed' ? now() : $contest->closed_at]);
        $this->audit($user, 'contest.status_changed', $contest, ['status' => $status]);
    }

    public function submitApplication(Contest $contest, array $candidateData, array $documents, string $source = 'public', ?User $user = null): ContestApplication
    {
        if (! $contest->isRegistrationOpen()) throw ValidationException::withMessages(['contest' => 'Les inscriptions à ce concours ne sont pas ouvertes.']);

        return DB::transaction(function () use ($contest, $candidateData, $documents, $source, $user): ContestApplication {
            $candidate = Candidate::where('email', $candidateData['email'])
                ->whereDate('birth_date', $candidateData['birth_date'])->first();
            $candidate ??= Candidate::create($candidateData);
            if (ContestApplication::whereBelongsTo($contest)->whereBelongsTo($candidate)->exists()) {
                throw ValidationException::withMessages(['email' => 'Une candidature existe déjà pour ce candidat et ce concours.']);
            }
            $application = ContestApplication::create([
                'contest_id' => $contest->id, 'candidate_id' => $candidate->id, 'status' => $source === 'admin' ? 'validated' : 'pending',
                'source' => $source, 'documents' => $documents, 'verification_code' => bin2hex(random_bytes(16)), 'submitted_at' => now(),
            ]);
            if ($source === 'admin') $this->assignIdentifiers($application);
            $this->audit($user, 'application.created', $application, ['source' => $source]);
            return $application->fresh('candidate');
        });
    }

    public function review(ContestApplication $application, string $status, ?string $observations, User $user): void
    {
        if (! in_array($status, ['validated', 'rejected', 'correction_required'], true)) throw ValidationException::withMessages(['status' => 'Décision invalide.']);
        DB::transaction(function () use ($application, $status, $observations, $user): void {
            if ($status === 'validated') $this->assignIdentifiers($application);
            $application->update(['status' => $status, 'observations' => $observations, 'reviewed_by' => $user->id, 'reviewed_at' => now()]);
            $this->audit($user, 'application.'.$status, $application);
        });
    }

    public function saveResults(Contest $contest, array $averages, User $user): void
    {
        if (! in_array($contest->status, ['completed', 'results_preparation', 'results_published'], true)) throw ValidationException::withMessages(['contest' => 'Le concours doit être terminé avant la saisie des résultats.']);
        DB::transaction(function () use ($contest, $averages, $user): void {
            foreach ($averages as $applicationId => $average) {
                if ($average === null || $average === '') continue;
                $application = $contest->applications()->where('status', 'validated')->findOrFail($applicationId);
                $score = round((float) $average, 2);
                if ($score < 0 || $score > 20) throw ValidationException::withMessages(["averages.{$applicationId}" => 'La moyenne doit être comprise entre 0 et 20.']);
                ContestResult::updateOrCreate(['contest_application_id' => $application->id], ['average' => $score, 'mention' => $this->mention($score), 'decision' => $this->decision($score)]);
            }
            $contest->update(['status' => 'results_preparation', 'results_validated_at' => null, 'published_at' => null, 'published_by' => null]);
            $this->recalculateRanks($contest);
            $this->audit($user, 'results.updated', $contest);
        });
    }

    public function validateResults(Contest $contest, User $user): void
    {
        $validated = $contest->applications()->where('status', 'validated')->count();
        $completed = $contest->applications()->where('status', 'validated')->whereHas('result')->count();
        if ($validated === 0 || $validated !== $completed) throw ValidationException::withMessages(['results' => 'Tous les candidats validés doivent avoir un résultat avant la validation.']);
        $contest->update(['status' => 'results_preparation', 'results_validated_at' => now()]);
        $this->audit($user, 'results.validated', $contest);
    }

    public function publish(Contest $contest, User $user): void
    {
        if (! $contest->results_validated_at) throw ValidationException::withMessages(['results' => 'Les résultats doivent être complets et validés avant publication.']);
        $contest->update(['status' => 'results_published', 'published_at' => now(), 'published_by' => $user->id]);
        $this->audit($user, 'results.published', $contest);
    }

    public function unpublish(Contest $contest, User $user): void
    {
        $contest->update(['status' => 'results_preparation', 'published_at' => null, 'published_by' => null]);
        $this->audit($user, 'results.unpublished', $contest);
    }

    public function mention(float $average): ?string
    {
        foreach (config('contests.mentions') as $threshold => $mention) if ($average >= $threshold) return $mention;
        return null;
    }

    public function decision(float $average): string { return $average >= config('contests.admission_average') ? 'admitted' : 'not_admitted'; }

    private function assignIdentifiers(ContestApplication $application): void
    {
        $candidate = $application->candidate()->lockForUpdate()->firstOrFail();
        $year = (int) substr($application->contest->academic_year, 0, 4);
        if (! $candidate->registration_number) {
            $next = Candidate::where('registration_number', 'like', "INSG-{$year}-%")->lockForUpdate()->count() + 1;
            $candidate->update(['registration_number' => sprintf('INSG-%d-%04d', $year, $next)]);
        }
        if (! $application->candidate_number) {
            $next = ContestApplication::where('contest_id', $application->contest_id)->whereNotNull('candidate_number')->lockForUpdate()->count() + 1;
            $application->update(['candidate_number' => $application->contest->reference.'-'.sprintf('%04d', $next)]);
        }
    }

    private function recalculateRanks(Contest $contest): void
    {
        $results = ContestResult::whereHas('application', fn ($q) => $q->where('contest_id', $contest->id))->orderByDesc('average')->orderBy('id')->get();
        $previous = null; $rank = 0;
        foreach ($results as $index => $result) {
            if ($previous === null || (float) $result->average !== $previous) $rank = $index + 1;
            $result->updateQuietly(['rank' => $rank]);
            $previous = (float) $result->average;
        }
    }

    private function audit(?User $user, string $action, Model $model, array $metadata = []): void
    {
        ContestAudit::create(['user_id' => $user?->id, 'action' => $action, 'auditable_type' => $model::class, 'auditable_id' => $model->getKey(), 'metadata' => $metadata, 'created_at' => now()]);
    }
}

<?php

namespace App\Services;

use App\Models\Contest;
use App\Models\ContestAudit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ContestService
{
    public function createContest(array $data, User $user): Contest
    {
        return DB::transaction(function () use ($data, $user): Contest {
            $year = (int) substr($data['academic_year'], 0, 4);
            $sequence = Contest::where('reference', 'like', "CONC-{$year}-%")->lockForUpdate()->count() + 1;
            $contest = Contest::create($data + ['reference' => sprintf('CONC-%d-%03d', $year, $sequence), 'status' => 'draft']);
            foreach ([1, 2] as $number) $contest->rounds()->create(['number' => $number]);
            $this->audit($user, 'contest.created', $contest);
            return $contest;
        });
    }

    private function audit(?User $user, string $action, Model $model, array $metadata = []): void
    {
        ContestAudit::create(['user_id' => $user?->id, 'action' => $action, 'auditable_type' => $model::class, 'auditable_id' => $model->getKey(), 'metadata' => $metadata, 'created_at' => now()]);
    }
}

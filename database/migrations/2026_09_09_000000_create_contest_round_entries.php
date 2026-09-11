<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_rounds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['contest_id', 'number']);
        });
        Schema::create('contest_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round');
            $table->string('last_name', 100);
            $table->string('first_names', 150);
            $table->string('field', 150);
            $table->decimal('average', 4, 2)->nullable();
            $table->string('decision', 100)->nullable();
            $table->unsignedInteger('rank')->nullable();
            $table->timestamps();
            $table->index(['contest_id', 'round', 'rank']);
        });
        // Preserve existing candidates and results as first-round records.
        DB::table('contest_applications')->join('candidates', 'candidates.id', '=', 'contest_applications.candidate_id')
            ->leftJoin('contest_tracks', 'contest_tracks.id', '=', 'contest_applications.contest_track_id')
            ->leftJoin('contest_results', 'contest_results.contest_application_id', '=', 'contest_applications.id')
            ->select('contest_applications.id', 'contest_applications.contest_id', 'candidates.last_name', 'candidates.first_names', 'candidates.field', 'contest_tracks.name as track_name', 'contest_results.average', 'contest_results.decision', 'contest_results.rank')
            ->orderBy('contest_applications.id')->each(function ($row): void {
                DB::table('contest_entries')->insert([
                    'contest_id' => $row->contest_id, 'round' => 1, 'last_name' => $row->last_name, 'first_names' => $row->first_names,
                    'field' => $row->track_name ?? $row->field ?? '', 'average' => $row->average,
                    'decision' => match ($row->decision) { 'admitted' => 'Admis', 'not_admitted' => 'Non admis', default => $row->decision },
                    'rank' => $row->rank, 'created_at' => now(), 'updated_at' => now(),
                ]);
            });
        foreach (DB::table('contests')->get() as $contest) {
            foreach ([1, 2] as $number) DB::table('contest_rounds')->insert([
                'contest_id' => $contest->id, 'number' => $number,
                'published_at' => $number === 1 && $contest->status === 'results_published' ? $contest->published_at : null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_entries');
        Schema::dropIfExists('contest_rounds');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_tracks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['contest_id', 'name']);
        });

        Schema::create('contest_subjects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_track_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('max_score', 6, 2)->default(20);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['contest_track_id', 'name']);
        });

        Schema::table('contest_applications', function (Blueprint $table): void {
            $table->foreignId('contest_track_id')->nullable()->after('contest_id')->constrained()->nullOnDelete();
        });

        Schema::create('contest_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contest_subject_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 6, 2);
            $table->timestamps();
            $table->unique(['contest_application_id', 'contest_subject_id']);
        });

        foreach (DB::table('contests')->pluck('id') as $contestId) {
            $trackId = DB::table('contest_tracks')->insertGetId(['contest_id' => $contestId, 'name' => 'Licence fondamentale', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]);
            foreach (['Français', 'Mathématiques', 'Anglais', 'Oral'] as $index => $name) DB::table('contest_subjects')->insert(['contest_track_id' => $trackId, 'name' => $name, 'max_score' => 20, 'sort_order' => $index + 1, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('contest_applications')->where('contest_id', $contestId)->update(['contest_track_id' => $trackId]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_scores');
        Schema::table('contest_applications', fn (Blueprint $table) => $table->dropConstrainedForeignId('contest_track_id'));
        Schema::dropIfExists('contest_subjects');
        Schema::dropIfExists('contest_tracks');
    }
};

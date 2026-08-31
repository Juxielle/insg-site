<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contests', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('academic_year')->index();
            $table->string('session');
            $table->string('type');
            $table->dateTime('registration_starts_at');
            $table->dateTime('registration_ends_at')->index();
            $table->date('exam_date');
            $table->time('exam_time');
            $table->string('location');
            $table->unsignedInteger('available_places');
            $table->string('status')->default('draft')->index();
            $table->text('additional_information')->nullable();
            $table->timestamp('results_validated_at')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('registration_number')->nullable()->unique();
            $table->string('last_name')->index();
            $table->string('first_names');
            $table->string('gender')->nullable();
            $table->date('birth_date');
            $table->string('birth_place')->nullable();
            $table->string('nationality');
            $table->string('photo_url')->nullable();
            $table->string('phone');
            $table->string('email')->index();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('study_level');
            $table->string('previous_school')->nullable();
            $table->string('diploma');
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('field')->nullable();
            $table->string('specialty')->nullable();
            $table->timestamps();
            $table->unique(['email', 'birth_date']);
        });

        Schema::create('contest_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->restrictOnDelete();
            $table->string('candidate_number')->nullable()->unique();
            $table->string('status')->default('pending')->index();
            $table->string('source')->default('public');
            $table->text('observations')->nullable();
            $table->json('documents')->nullable();
            $table->string('verification_code', 64)->unique();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['contest_id', 'candidate_id']);
        });

        Schema::create('contest_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contest_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('average', 4, 2);
            $table->string('mention')->nullable();
            $table->string('decision');
            $table->unsignedInteger('rank')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('contest_audits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_audits');
        Schema::dropIfExists('contest_results');
        Schema::dropIfExists('contest_applications');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('contests');
    }
};

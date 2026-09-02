<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            $table->string('tracking_number')->nullable()->unique()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            $table->dropUnique(['tracking_number']);
            $table->dropColumn('tracking_number');
        });
    }
};

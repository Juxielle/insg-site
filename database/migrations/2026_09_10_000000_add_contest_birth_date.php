<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contest_entries', fn (Blueprint $table) => $table->date('birth_date')->nullable());
        DB::table('contest_entries')->whereIn('decision', ['Admis', 'admitted', 'ADMIS', 'Admis(e)'])->update(['decision' => 'Admis(e)']);
        DB::table('contest_entries')->whereIn('decision', ['Non admis', 'not_admitted', 'Ajourné', 'Ajournée', 'Ajourné(e)'])->update(['decision' => 'Ajourné(e)']);
    }

    public function down(): void
    {
        Schema::table('contest_entries', fn (Blueprint $table) => $table->dropColumn('birth_date'));
    }
};

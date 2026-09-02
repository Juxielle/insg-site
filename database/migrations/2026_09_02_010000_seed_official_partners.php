<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groups = [
            'Banque et finance' => ['BGFI', 'FINAM', 'BICIG', 'UGB', 'La Régionale d’Épargne et de Crédit du Gabon', 'Olsen Courtage', 'Luxo Finance'],
            'Audit et conseil' => ['KPMG', 'Deloitte', 'Ernst & Young', 'IMPROM', 'AC Consulting', 'K.T.A.', 'Cabinet Ramses', 'Salmane International Trade', 'Cabinet Fidicia', 'Metodik Audit & Conseils', '2AD Consulting'],
            'Industrie et services' => ['SOGAFRIC / SODIM TP / GESPARC', 'Bernabé Gabon', 'EDG SA', 'CAISTAB', 'SEEG', 'PERENCO', 'LABOREX', 'Vocal Centre'],
            'Transport et logistique' => ['SETRAG', 'BM-Transit', 'DHL', 'ASECNA', 'OCT Owendo', 'Gabon Port Management', 'AVS International', 'Soma Trans International'],
            'Institutions publiques' => ['Conseil Gabonais des Chargeurs', 'Ministère de la Santé', 'DGCPT', 'Ministère des Comptes publics'],
        ];

        $names = collect($groups)->flatten()->values()->all();
        DB::table('partners')->whereNotIn('name', $names)->update(['active' => false, 'updated_at' => now()]);

        foreach ($groups as $category => $partners) {
            foreach ($partners as $name) {
                DB::table('partners')->updateOrInsert(
                    ['name' => $name],
                    ['category' => $category, 'active' => true, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        // Les partenaires sont des contenus éditoriaux : aucune suppression automatique.
    }
};

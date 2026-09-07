<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('slug', 'about')->value('id');
        if (! $pageId) return;

        DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('key', 'leadership')
            ->update([
                'body' => '<p><strong>Pr Murielle Natacha M’BOUNA</strong></p><p>Maître de conférences, agrégée des universités en sciences de gestion.</p><p><strong>Responsabilités académiques</strong><br>Directeur des Études en charge du premier cycle, de la Licence fondamentale, de la Licence professionnelle et du BTS à l’Institut National des Sciences de Gestion (INSG).</p><p><strong>Adresse institutionnelle</strong><br>BP 190 INSG, Gros Bouquet, Libreville.</p><p><strong>Activités de recherche</strong><br>Responsable du Groupe de Recherche en Gestion.<br>Laboratoire CIREGED, Université Omar Bongo, Libreville.</p>',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Le contenu éditorial antérieur n’est pas restauré automatiquement.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('slug', 'about')->value('id');
        if (! $pageId) return;

        DB::table('page_sections')->where('page_id', $pageId)->where('key', 'governance')->update([
            'name' => 'Organigramme institutionnel',
            'type' => 'rich_text',
            'body' => 'Une vue d’ensemble claire de la gouvernance, des pôles et des services qui structurent le fonctionnement administratif et pédagogique de l’INSG.',
            'eyebrow' => 'Structure de l’Institut',
            'title' => 'Comment l’INSG est organisé',
            'image_url' => '/assets/images/organigramme-insg.jpeg',
            'items' => null,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Le contenu éditorial antérieur n’est pas restauré automatiquement.
    }
};

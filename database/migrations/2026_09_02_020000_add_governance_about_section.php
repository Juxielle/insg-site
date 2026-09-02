<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('slug', 'about')->value('id');
        if (! $pageId) return;

        $items = [
            ['icon'=>'bi-diagram-3','title'=>'Instances décisionnelles','text'=>'<ul><li><strong>Conseil d’Administration :</strong> définit les orientations stratégiques.</li><li><strong>Direction générale :</strong> assure la mise en œuvre des décisions.</li><li><strong>Secrétariat général :</strong> coordonne les services administratifs et pédagogiques.</li><li><strong>Directions des études :</strong> structurent et organisent le fonctionnement pédagogique.</li></ul>'],
            ['icon'=>'bi-building-gear','title'=>'Services opérationnels','text'=>'<ul><li>Scolarité</li><li>Personnel</li><li>Affaires financières</li><li>Relations entreprises</li><li>Service juridique</li><li>Cellule interne d’assurance qualité</li><li>Service informatique</li><li>Service social et psychologique</li><li>Police universitaire</li></ul>'],
            ['icon'=>'bi-mortarboard','title'=>'Organisation pédagogique','text'=>'<ul><li>Directions des études 1 et 2</li><li>Départements</li><li>Laboratoires</li><li>Centre de documentation</li><li>Cellule interne d’assurance qualité</li></ul><p>Ces structures coordonnent les enseignements, la recherche, la documentation et le suivi de la qualité académique.</p>'],
        ];

        DB::table('page_sections')->updateOrInsert(
            ['page_id' => $pageId, 'key' => 'governance'],
            ['name' => 'Gouvernance et organisation institutionnelle', 'type' => 'cards', 'body' => 'L’INSG s’appuie sur une gouvernance qui articule les instances décisionnelles, les services opérationnels et les structures pédagogiques. Cette organisation accompagne la modernisation de l’Institut, la professionnalisation des formations et le renforcement continu de la qualité.', 'eyebrow' => 'Fonctionnement de l’Institut', 'title' => 'Gouvernance et organisation institutionnelle', 'items' => json_encode($items, JSON_UNESCAPED_UNICODE), 'theme' => 'light', 'sort_order' => 50, 'active' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('page_sections')->where('page_id', $pageId)->where('key', 'leadership')->update(['sort_order' => 60, 'updated_at' => now()]);
    }

    public function down(): void
    {
        $pageId = DB::table('pages')->where('slug', 'about')->value('id');
        DB::table('page_sections')->where('page_id', $pageId)->where('key', 'governance')->delete();
    }
};

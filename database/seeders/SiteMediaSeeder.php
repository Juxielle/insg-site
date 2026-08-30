<?php

namespace Database\Seeders;

use App\Models\SiteMedia;
use Illuminate\Database\Seeder;

class SiteMediaSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['site_logo', 'Logo principal du site', '/assets/images/insg-logo.jpeg', 'Logo INSG Gabon'],
            ['social_cover', 'Image de partage sur les réseaux sociaux', '/assets/images/insg1.webp', 'Campus de l’INSG Gabon'],
            ['home_hero_poster', 'Image d’attente de la vidéo d’accueil', '/assets/images/insg1.webp', 'Campus de l’INSG Gabon'],
            ['inner_page_hero', 'Fond des en-têtes des pages internes', '/assets/images/building.png', 'Bâtiment de l’INSG Gabon'],
            ['home_student_1', 'Portrait étudiant 1 — accueil', '/assets/images/insg2.jpg', 'Étudiant de l’INSG'],
            ['home_student_2', 'Portrait étudiant 2 — accueil', '/assets/images/insg3.jpg', 'Étudiante de l’INSG'],
            ['home_student_3', 'Portrait étudiant 3 — accueil', '/assets/images/insg4.jpg', 'Étudiant de l’INSG'],
        ] as [$key, $label, $imageUrl, $altText]) {
            SiteMedia::updateOrCreate(['key' => $key], [
                'label' => $label,
                'image_url' => $imageUrl,
                'alt_text' => $altText,
            ]);
        }
    }
}

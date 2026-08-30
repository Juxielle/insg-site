<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['home', 'Accueil', 'INSG Gabon — Institut National des Sciences de Gestion', "L'Institut National des Sciences de Gestion forme les futurs cadres et managers du Gabon.", 'Formez l’excellence, construisez votre avenir de manager', "L'INSG Gabon prépare les cadres, managers et entrepreneurs qui font la performance des organisations gabonaises."],
            ['about', 'À propos', "À propos de l'INSG", "Découvrez l'histoire, la mission et la gouvernance de l'INSG Gabon.", "À propos de l'INSG", "Découvrez notre histoire, notre mission et les femmes et hommes qui portent l'excellence académique au service du Gabon."],
            ['formations', 'Formations', 'Nos formations | INSG Gabon', "Découvrez les Licences, Masters et parcours professionnels proposés par l'INSG.", 'Nos formations', "Découvrez les parcours accessibles selon votre série de baccalauréat et construisez votre projet professionnel."],
            ['admissions', 'Admissions', 'Admissions | INSG Gabon', "Conditions, procédures et formulaire de pré-inscription à l'INSG.", "Admissions à l'INSG", "Toutes les informations utiles pour préparer votre candidature et rejoindre l'INSG."],
            ['actualites', 'Actualités', "Actualités de l'INSG", "Suivez les actualités académiques, partenariats et événements de l'INSG.", "Actualités de l'INSG", "Admissions, partenariats, événements et vie académique : suivez toute l'actualité de l'institut."],
            ['annonces-concours', 'Annonces et concours', "Annonces de concours | INSG", "Avis officiels, concours et appels à candidatures de l'INSG.", 'Annonces de concours', "Retrouvez les avis officiels de concours, appels à candidatures et procédures de sélection publiés par l'INSG."],
            ['vie-etudiante', 'Vie étudiante', "Vie étudiante | INSG", "Clubs, événements et activités étudiantes de l'INSG.", 'Vie étudiante', "Clubs, associations, sport et culture : la vie à l'INSG ne s'arrête pas aux salles de cours."],
            ['entreprises', 'Partenaires', "Partenaires et entreprises | INSG", "Découvrez les partenaires et opportunités professionnelles de l'INSG.", 'Espace Partenaires', "Entreprises, institutions et diplômés : construisons ensemble les compétences dont le Gabon a besoin."],
            ['bibliotheque', 'Bibliothèque', "Bibliothèque universitaire | INSG", "Ressources documentaires et services de la bibliothèque de l'INSG.", 'Bibliothèque universitaire', "Des ressources académiques et professionnelles pour accompagner vos études et vos travaux de recherche."],
            ['recherche', 'Recherche', "Recherche en Sciences de Gestion | INSG", "Laboratoire, publications et activités scientifiques de l'INSG.", 'Recherche en Sciences de Gestion', "Produire des connaissances utiles aux organisations, aux décideurs et au développement du Gabon."],
            ['incubateur', 'Incubateur', "Incubateur de projets | INSG", "Accompagnement entrepreneurial et incubation de projets à l'INSG.", 'Incubateur de projets INSG', "Transformez une idée en projet viable grâce à un accompagnement structuré et à un réseau d'experts."],
            ['inscription-master', 'Inscription en Master', "Candidature en Master | INSG", "Déposez votre candidature en Master à l'INSG.", 'Candidature en Master', "Candidatez en ligne à l'une des spécialités de Master de l'INSG. L'admission est fondée sur l'analyse de votre dossier."],
            ['contact', 'Contact', "Contactez l'INSG", "Coordonnées et formulaire de contact de l'INSG Gabon.", 'Contactez-nous', "Une question sur nos formations, les admissions ou un partenariat ? Notre équipe vous répond."],
        ];

        foreach ($pages as [$slug, $name, $metaTitle, $metaDescription, $heroTitle, $heroText]) {
            Page::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'hero_title' => $heroTitle,
                'hero_text' => $heroText,
                'active' => true,
            ]);
        }
    }
}

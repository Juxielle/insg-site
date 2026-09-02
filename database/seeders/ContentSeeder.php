<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Program;
use App\Models\SiteStatistic;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['Finance', 'finance', 'finance', 'Licence & Master', '5 ans', 'bi-cash-coin', 'Analyse financière, ingénierie financière et gestion de portefeuille.', ['Analyste financier', 'Trésorier', 'Gestionnaire de portefeuille'], ['Mathématiques financières', 'Marchés financiers', 'Gestion des risques']],
            ['Comptabilité', 'comptabilite', 'comptabilite', 'Licence & Master', '5 ans', 'bi-calculator', 'Comptabilité générale, fiscalité et contrôle de gestion appliqués.', ['Chef comptable', 'Contrôleur de gestion', 'Expert-comptable'], ['SYSCOHADA', 'Fiscalité', 'Audit comptable']],
            ['Marketing', 'marketing', 'marketing', 'Licence & Master', '5 ans', 'bi-megaphone', 'Stratégie de marque, communication et marketing digital.', ['Chef de produit', 'Responsable marketing', 'Community manager'], ['Études de marché', 'Marketing digital', 'Communication']],
            ['Ressources Humaines', 'ressources-humaines', 'rh', 'Licence & Master', '5 ans', 'bi-people', 'Gestion des talents, droit social et développement organisationnel.', ['Responsable RH', 'Chargé de recrutement', 'Gestionnaire de paie'], ['Droit social', 'Gestion des talents', 'Paie']],
            ['Management', 'management', 'management', 'Licence & Master', '5 ans', 'bi-diagram-3', 'Pilotage stratégique et opérationnel des organisations.', ['Manager', 'Consultant', 'Entrepreneur'], ['Stratégie', 'Leadership', 'Gestion de projet']],
            ['Banque', 'banque', 'banque', 'Licence & Master', '5 ans', 'bi-bank', 'Métiers bancaires, crédit, conformité et relation clientèle.', ['Chargé de clientèle', 'Analyste crédit', 'Responsable conformité'], ['Techniques bancaires', 'Crédit', 'Conformité']],
            ['Audit & Contrôle', 'audit-controle', 'audit', 'Licence & Master', '5 ans', 'bi-clipboard-data', 'Audit interne, contrôle des comptes et maîtrise des risques.', ['Auditeur', 'Contrôleur interne', 'Risk manager'], ['Audit interne', 'Contrôle de gestion', 'Gestion des risques']],
            ['Informatique de Gestion', 'informatique-gestion', 'informatique', 'Licence & Master', '5 ans', 'bi-laptop', "Systèmes d'information, ERP et transformation digitale.", ['Chef de projet SI', 'Consultant ERP', 'Data analyst'], ['Bases de données', 'ERP', 'Gestion de projet SI']],
        ];

        foreach ($programs as $index => [$name, $slug, $category, $level, $duration, $icon, $description, $opportunities, $curriculum]) {
            Program::updateOrCreate(['slug' => $slug], compact('name', 'category', 'level', 'duration', 'icon', 'description', 'opportunities', 'curriculum') + ['featured' => true, 'active' => true, 'sort_order' => $index + 1]);
        }

        $articles = [
            ['Ouverture des inscriptions pour la rentrée académique 2026-2027', 'Admissions', 'Les candidatures sont ouvertes pour toutes les filières de licence et de master.', '2026-08-15', 'Rentrée 2026', true],
            ["Signature d'un partenariat stratégique avec BGFI Bank", 'Partenariats', "Ce partenariat renforce l'insertion professionnelle et les stages de nos étudiants.", '2026-08-08', 'Partenariat', true],
            ["Forum de l'emploi et des stages : plus de 30 entreprises attendues", 'Vie étudiante', 'Étudiants et diplômés rencontreront directement les recruteurs du territoire.', '2026-07-29', 'Forum Emploi', true],
            ['Les étudiants de Finance remportent le Challenge CEMAC', 'Réussite', "L'équipe INSG s'est distinguée lors de la finale régionale.", '2026-07-18', 'Challenge', false],
            ['Une nouvelle salle numérique pour la pédagogie active', 'Campus', 'Le nouvel espace offre des équipements collaboratifs de dernière génération.', '2026-07-05', 'Campus numérique', false],
            ['Conférence sur la finance durable en Afrique centrale', 'Recherche', 'Chercheurs et professionnels ont partagé leurs perspectives sur la transition.', '2026-06-20', 'Finance durable', false],
        ];
        $articleImages = [
            '/assets/images/rentree.png',
            '/assets/images/partenariat.png',
            '/assets/images/forum.png',
        ];
        foreach ($articles as $index => [$title, $category, $excerpt, $published, $imageText, $featured]) {
            $slug = str($title)->slug();
            Article::updateOrCreate(['slug' => $slug], ['title' => $title, 'category' => $category, 'excerpt' => $excerpt, 'content' => $excerpt, 'image_url' => $articleImages[$index] ?? 'https://placehold.co/500x350/0d2f6e/ffffff?text='.urlencode($imageText), 'published_at' => $published, 'featured' => $featured]);
        }

        foreach ([
            ['Journée Portes Ouvertes 2026', 'Campus INSG, Libreville', '2026-09-05 09:00', '2026-09-05 16:00'],
            ['Conférence « Finance durable en Afrique Centrale »', 'Amphithéâtre A', '2026-09-14 14:00', '2026-09-14 17:00'],
            ["Forum de l'emploi et des stages", 'Grande cour INSG', '2026-09-22 08:30', '2026-09-22 17:30'],
        ] as [$title, $location, $startsAt, $endsAt]) {
            Event::updateOrCreate(['title' => $title], ['location' => $location, 'starts_at' => $startsAt, 'ends_at' => $endsAt]);
        }

        foreach ([
            ['Concours d’entrée en Licence 1 — Session 2026', 'Concours', 'Les inscriptions au concours sont ouvertes aux titulaires du baccalauréat.', '2026-08-01', '2026-09-10', 'open'],
            ['Recrutement de vacataires — Département Finance', 'Recrutement', 'Appel à candidatures pour des enseignements spécialisés en finance.', '2026-08-10', '2026-09-02', 'open'],
            ['Résultats du concours Master professionnel', 'Résultats', 'La liste des candidats admis est disponible auprès de la scolarité.', '2026-08-12', null, 'published'],
        ] as [$title, $type, $description, $publishedAt, $deadlineAt, $status]) {
            Announcement::updateOrCreate(['title' => $title], [
                'type' => $type, 'description' => $description,
                'published_at' => $publishedAt, 'deadline_at' => $deadlineAt, 'status' => $status,
            ]);
        }

        $partners = [
            'Banque et finance' => ['BGFI', 'FINAM', 'BICIG', 'UGB', 'La Régionale d’Épargne et de Crédit du Gabon', 'Olsen Courtage', 'Luxo Finance'],
            'Audit et conseil' => ['KPMG', 'Deloitte', 'Ernst & Young', 'IMPROM', 'AC Consulting', 'K.T.A.', 'Cabinet Ramses', 'Salmane International Trade', 'Cabinet Fidicia', 'Metodik Audit & Conseils', '2AD Consulting'],
            'Industrie et services' => ['SOGAFRIC / SODIM TP / GESPARC', 'Bernabé Gabon', 'EDG SA', 'CAISTAB', 'SEEG', 'PERENCO', 'LABOREX', 'Vocal Centre'],
            'Transport et logistique' => ['SETRAG', 'BM-Transit', 'DHL', 'ASECNA', 'OCT Owendo', 'Gabon Port Management', 'AVS International', 'Soma Trans International'],
            'Institutions publiques' => ['Conseil Gabonais des Chargeurs', 'Ministère de la Santé', 'DGCPT', 'Ministère des Comptes publics'],
        ];
        foreach ($partners as $category => $names) {
            foreach ($names as $name) {
                Partner::updateOrCreate(['name' => $name], ['category' => $category, 'active' => true]);
            }
        }

        foreach ([
            ['Sarah Mboumba', 'Diplômée Finance, promotion 2024', "L'INSG m'a donné des bases solides et une vraie proximité avec le monde professionnel.", 'SM'],
            ['Grâce Obiang', 'Étudiante en Master RH', 'Les enseignants nous accompagnent dans des projets concrets et exigeants.', 'GO'],
            ['Paul Nzue', 'Entrepreneur, promotion 2022', "L'incubateur a transformé mon idée en une entreprise viable.", 'PN'],
        ] as [$name, $role, $quote, $initials]) {
            Testimonial::updateOrCreate(['name' => $name], ['role' => $role, 'quote' => $quote, 'avatar_url' => "https://placehold.co/100x100/0d2f6e/ffffff?text={$initials}", 'featured' => true]);
        }

        foreach ([
            ['students_per_year', 'Étudiants formés chaque année', 3200, '+'],
            ['programs', 'Filières spécialisées', 8, null],
            ['teachers', 'Enseignants & experts', 140, '+'],
            ['employment_rate', "Taux d'insertion professionnelle", 92, '%'],
        ] as $index => [$key, $label, $value, $suffix]) {
            SiteStatistic::updateOrCreate(['key' => $key], compact('label', 'value', 'suffix') + ['sort_order' => $index + 1]);
        }
    }
}

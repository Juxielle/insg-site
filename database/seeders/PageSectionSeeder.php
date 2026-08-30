<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $cards = fn (...$items) => $items;
        $sections = [
            'home' => [
                ['home-statistics', 'Chiffres clés', 'statistics', 'Les indicateurs publiés depuis le backend présentent l’activité et l’impact de l’institut.', 'L’INSG en chiffres', 'Une institution engagée dans la réussite'],
                ['director-message', 'Mot du Directeur Général', 'rich_text', '<p>Depuis sa création, l’Institut National des Sciences de Gestion s’est imposé comme une référence dans la formation des cadres gabonais. Notre ambition associe rigueur académique, ouverture internationale et proximité avec le monde professionnel.</p><p><strong>Pr. Murielle Natacha M’BOUNA</strong><br>Directeur Général, INSG Gabon</p>', 'Mot du Directeur Général', 'Former des managers responsables et compétitifs', '/assets/images/directeur-1.png'],
                ['home-programs', 'Formations à la une', 'programs', 'Les formations actives et mises en avant dans le backend sont présentées ici.', 'Notre offre académique', 'Des parcours conçus pour les métiers de demain'],
                ['campus-life', 'Cadre d’études', 'cards', 'Le campus offre des espaces conçus pour apprendre, collaborer et développer les projets étudiants.', 'Vie sur le campus', 'Un environnement propice à la réussite', null, $cards(['image'=>'/assets/images/building.png','icon'=>'bi-building','title'=>'Amphithéâtres','text'=>'Des espaces adaptés aux cours, conférences et rencontres académiques.'],['image'=>'/assets/images/library.png','icon'=>'bi-book','title'=>'Bibliothèque','text'=>'Des ressources documentaires pour les études et la recherche.'],['icon'=>'bi-wifi','title'=>'Services numériques','text'=>'Des outils numériques au service de la pédagogie et de la collaboration.'])],
                ['home-articles', 'Actualités récentes', 'articles', 'Les dernières publications sont affichées automatiquement selon leur date de publication.', 'Actualités', 'Les dernières actualités de l’INSG'],
                ['home-events', 'Agenda', 'events', 'Retrouvez les prochains rendez-vous publiés par l’administration.', 'Événements', 'Les prochains rendez-vous'],
                ['home-testimonials', 'Témoignages', 'testimonials', 'Étudiants et diplômés partagent leur expérience de formation et leur parcours.', 'Ils parlent de l’INSG', 'Des parcours qui inspirent'],
                ['home-partners', 'Partenaires', 'partners', 'Les partenaires actifs accompagnent la professionnalisation et l’ouverture de l’institut.', 'Ils nous font confiance', 'Nos partenaires institutionnels et professionnels'],
                ['home-cta', 'Appel à candidature', 'cta', 'Découvrez les conditions d’admission et préparez votre dossier.', null, 'Prêt à rejoindre l’INSG ?', null, null, 'Découvrir les admissions', '/pages/admissions.html'],
            ],
            'about' => [
                ['history', 'Notre histoire', 'rich_text', 'Depuis sa création, l’INSG accompagne la modernisation des organisations gabonaises en formant des cadres immédiatement opérationnels. Son projet pédagogique associe exigence académique, professionnalisation et ouverture sur l’environnement économique.', 'Institution', 'Un institut historique au service du Gabon', '/assets/images/building.png'],
                ['values', 'Mission et valeurs', 'cards', 'Notre action repose sur des engagements partagés par toute la communauté académique.', 'Nos engagements', 'Ce qui guide notre action au quotidien', null, $cards(['icon'=>'bi-award','title'=>'Excellence','text'=>'Une formation exigeante, actualisée et orientée vers la réussite.'],['icon'=>'bi-shield-check','title'=>'Intégrité','text'=>'Éthique, responsabilité et transparence dans toutes nos activités.'],['icon'=>'bi-lightbulb','title'=>'Innovation','text'=>'Des méthodes pédagogiques ouvertes aux transformations numériques.'])],
                ['leadership', 'Direction', 'rich_text', '<p><strong>Pr. Murielle Natacha M’BOUNA</strong><br>Directeur Général de l’INSG Gabon</p><p>La direction porte une ambition claire : former des managers responsables et compétitifs, capables de contribuer durablement au développement du pays.</p>', 'Gouvernance', 'Une direction engagée pour la réussite', '/assets/images/directeur-1.png'],
            ],
            'formations' => [
                ['catalogue', 'Catalogue des formations', 'programs', 'Chaque formation publiée depuis le backend apparaît automatiquement dans ce catalogue.', 'Orientation académique', 'Choisissez le parcours qui correspond à votre projet'],
                ['formation-cta', 'Appel à candidature', 'cta', 'Consultez les conditions d’admission et transmettez votre dossier en ligne.', null, 'Prêt à construire votre avenir professionnel ?', null, null, 'Candidater', '/pages/admissions.html'],
            ],
            'admissions' => [
                ['process', 'Procédure', 'cards', 'L’admission est organisée selon un parcours transparent, de la préparation du dossier à la confirmation définitive.', 'Votre candidature', 'Comment rejoindre l’INSG ?', null, $cards(['icon'=>'bi-search','title'=>'Choisir sa formation','text'=>'Consultez les parcours et vérifiez les conditions d’accès.'],['icon'=>'bi-folder-check','title'=>'Préparer le dossier','text'=>'Rassemblez les pièces académiques et administratives demandées.'],['icon'=>'bi-send-check','title'=>'Déposer la candidature','text'=>'Complétez le formulaire et transmettez des documents lisibles.'])],
                ['admission-form', 'Pré-inscription', 'admission_form', 'Les champs obligatoires permettent au service des admissions d’étudier votre demande.', 'Candidature', 'Formulaire de pré-inscription'],
            ],
            'actualites' => [['news-list', 'Toutes les actualités', 'articles', 'Les publications sont ajoutées, modifiées et retirées directement depuis le backend.', 'Publications', 'Toute l’actualité de l’INSG']],
            'annonces-concours' => [
                ['announcement-list', 'Annonces officielles', 'announcements', 'Consultez les appels à candidatures, concours et avis officiels publiés par l’administration.', 'Avis officiels', 'Candidatures et sélections'],
                ['announcement-advice', 'Conseils aux candidats', 'cards', 'Préparez un dossier complet et respectez exclusivement les informations publiées par l’institut.', 'Avant de candidater', 'Bien préparer votre dossier', null, $cards(['icon'=>'bi-shield-check','title'=>'Source officielle','text'=>'Fiez-vous aux dates et modalités publiées sur le site.'],['icon'=>'bi-folder-check','title'=>'Dossier complet','text'=>'Déposez des fichiers lisibles et conformes avant la date limite.'],['icon'=>'bi-envelope-check','title'=>'Confirmation','text'=>'Conservez l’accusé de réception transmis après le dépôt.'])],
            ],
            'vie-etudiante' => [
                ['student-clubs', 'Clubs et associations', 'cards', 'Les activités associatives développent le leadership, la créativité et l’engagement citoyen.', 'Vie du campus', 'Développez vos talents en dehors des cours', null, $cards(['icon'=>'bi-graph-up','title'=>'Finance et investissement','text'=>'Analyses de cas, simulations et rencontres professionnelles.'],['icon'=>'bi-mic','title'=>'Art oratoire et débat','text'=>'Ateliers de prise de parole et compétitions inter-écoles.'],['icon'=>'bi-laptop','title'=>'Digital et innovation','text'=>'Veille technologique, hackathons et entrepreneuriat numérique.'])],
                ['student-events', 'Agenda étudiant', 'events', 'Les rendez-vous publiés dans le backend sont affichés automatiquement.', 'Agenda', 'Les prochains événements'],
            ],
            'entreprises' => [
                ['partner-benefits', 'Collaborer avec l’INSG', 'cards', 'Les partenariats rapprochent la formation des besoins réels des organisations.', 'Relations entreprises', 'Construisons les compétences de demain', null, $cards(['icon'=>'bi-person-workspace','title'=>'Stages et emplois','text'=>'Rencontrez des étudiants formés aux réalités professionnelles.'],['icon'=>'bi-journal-check','title'=>'Projets pédagogiques','text'=>'Proposez des cas réels, conférences et projets tuteurés.'],['icon'=>'bi-lightbulb','title'=>'Innovation','text'=>'Développez des initiatives de recherche et d’entrepreneuriat.'])],
                ['partner-list', 'Nos partenaires', 'partners', 'Seuls les partenaires actifs dans le backend sont affichés.', 'Ils nous font confiance', 'Un réseau engagé aux côtés de l’INSG'],
            ],
            'bibliotheque' => [
                ['library-services', 'Services documentaires', 'cards', 'La bibliothèque soutient les apprentissages, la recherche et la préparation des projets.', 'Ressources', 'Un espace de travail au cœur du campus', null, $cards(['icon'=>'bi-book','title'=>'Collections spécialisées','text'=>'Ouvrages, mémoires et revues en économie, gestion et management.'],['icon'=>'bi-laptop','title'=>'Ressources numériques','text'=>'Accès à des bases documentaires et publications électroniques.'],['icon'=>'bi-people','title'=>'Accompagnement','text'=>'Orientation bibliographique et aide à la recherche documentaire.'])],
                ['library-access', 'Accès', 'rich_text', '<p>Les étudiants et enseignants peuvent consulter les ressources sur place selon les horaires communiqués par l’institut. Les services numériques nécessitent une authentification institutionnelle.</p>', 'Informations pratiques', 'Consulter et travailler dans de bonnes conditions', '/assets/images/library.png'],
            ],
            'recherche' => [
                ['research-lab', 'Laboratoire', 'rich_text', '<p>Le Laboratoire de Recherche en Sciences de Gestion développe des travaux utiles aux entreprises, aux administrations et aux décideurs publics. Il favorise la diffusion scientifique et les collaborations interdisciplinaires.</p>', 'LARSG', 'Produire des connaissances utiles au développement', '/assets/images/building.png'],
                ['research-areas', 'Axes de recherche', 'cards', 'Les projets scientifiques couvrent les principaux enjeux contemporains des organisations.', 'Expertise', 'Domaines de recherche prioritaires', null, $cards(['icon'=>'bi-bank','title'=>'Finance et gouvernance','text'=>'Performance financière, risques et gouvernance des organisations.'],['icon'=>'bi-people','title'=>'Management et RH','text'=>'Leadership, compétences et transformation des organisations.'],['icon'=>'bi-globe-africa','title'=>'Développement durable','text'=>'Responsabilité sociale, territoires et transitions économiques.'])],
            ],
            'incubateur' => [
                ['incubator-purpose', 'Mission de l’incubateur', 'rich_text', '<p>L’incubateur accompagne les étudiants et jeunes diplômés depuis l’idée initiale jusqu’à la validation d’un modèle économique viable. Les porteurs de projet bénéficient de méthodes, de mentorat et d’un réseau professionnel.</p>', 'Entrepreneuriat', 'De l’idée à l’entreprise'],
                ['incubator-program', 'Programme', 'cards', 'Un accompagnement progressif permet de transformer une intention en projet structuré.', 'Parcours', 'Un programme en quatre étapes', null, $cards(['icon'=>'bi-lightbulb','title'=>'Idéation','text'=>'Clarifier le besoin, la proposition de valeur et les bénéficiaires.'],['icon'=>'bi-clipboard-data','title'=>'Validation','text'=>'Étudier le marché et tester les hypothèses principales.'],['icon'=>'bi-rocket-takeoff','title'=>'Lancement','text'=>'Construire le modèle économique et préparer la mise sur le marché.'])],
                ['incubator-cta', 'Candidater', 'cta', 'Présentez votre projet, vos motivations et les premiers éléments de validation.', null, 'Osez entreprendre avec l’INSG', null, null, 'Nous contacter', '/pages/contact.html'],
            ],
            'inscription-master' => [
                ['master-process', 'Sélection', 'cards', 'La commission évalue la cohérence du parcours, les résultats académiques, la motivation et le projet professionnel.', 'Admission sur dossier', 'Une sélection attentive de votre parcours', null, $cards(['icon'=>'bi-upload','title'=>'Dépôt','text'=>'Complétez le formulaire et joignez toutes les pièces demandées.'],['icon'=>'bi-search','title'=>'Vérification','text'=>'Le service des admissions contrôle la conformité du dossier.'],['icon'=>'bi-check2-circle','title'=>'Décision','text'=>'La décision et les modalités d’inscription sont communiquées par email.'])],
                ['master-form', 'Formulaire Master', 'master_form', 'Préparez vos diplômes, relevés, CV, lettre de motivation et pièce d’identité avant de commencer.', 'Candidature en ligne', 'Déposer une candidature en Master'],
            ],
            'contact' => [
                ['contact-details', 'Coordonnées', 'cards', 'Notre équipe répond aux demandes relatives aux formations, admissions, partenariats et services.', 'Nous joindre', 'Les moyens de contacter l’INSG', null, $cards(['icon'=>'bi-geo-alt','title'=>'Adresse','text'=>'Boulevard Triomphal, Libreville, Gabon'],['icon'=>'bi-telephone','title'=>'Téléphone','text'=>'+241 01 23 45 67'],['icon'=>'bi-envelope','title'=>'Email','text'=>'contact@insg-gabon.ga'])],
                ['contact-form', 'Formulaire de contact', 'contact_form', 'Décrivez précisément votre demande afin qu’elle soit orientée vers le bon service.', 'Écrivez-nous', 'Envoyer un message'],
            ],
        ];

        foreach ($sections as $pageSlug => $pageSections) {
            $page = Page::where('slug', $pageSlug)->firstOrFail();
            foreach ($pageSections as $order => $values) {
                [$key, $name, $type, $body, $eyebrow, $title, $imageUrl, $items, $buttonLabel, $buttonUrl] = array_pad($values, 10, null);
                PageSection::updateOrCreate(['page_id' => $page->id, 'key' => $key], [
                    'name' => $name, 'type' => $type, 'body' => $body, 'eyebrow' => $eyebrow,
                    'title' => $title, 'image_url' => $imageUrl, 'items' => $items,
                    'button_label' => $buttonLabel, 'button_url' => $buttonUrl,
                    'theme' => $type === 'cta' ? 'navy' : ($order % 2 ? 'gray' : 'light'),
                    'sort_order' => ($order + 1) * 10, 'active' => true,
                ]);
            }
        }
    }
}

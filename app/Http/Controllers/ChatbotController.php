<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function answer(Request $request): JsonResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:500']]);
        $question = Str::lower(Str::ascii($data['message']));

        [$answer, $links] = match (true) {
            Str::contains($question, ['formation', 'filiere', 'licence', 'master', 'programme']) => $this->programsAnswer(),
            Str::contains($question, ['admission', 'inscription', 'candidature', 'postuler', 'dossier']) => [
                'Vous pouvez consulter les conditions d’admission et déposer votre candidature depuis la page Admissions. Pour un Master, utilisez le formulaire d’inscription dédié.',
                $this->links(['Admissions' => route('pages.admissions'), 'Inscription Master' => route('pages.inscription-master')]),
            ],
            Str::contains($question, ['contact', 'telephone', 'email', 'adresse', 'localisation']) => [
                'L’INSG est situé à Libreville. Vous pouvez envoyer votre demande depuis le formulaire de contact ; l’équipe administrative vous répondra directement.',
                $this->links(['Nous contacter' => route('pages.contact')]),
            ],
            Str::contains($question, ['concours', 'annonce', 'date limite']) => $this->announcementsAnswer(),
            Str::contains($question, ['actualite', 'nouvelle', 'evenement']) => $this->newsAnswer(),
            Str::contains($question, ['connexion', 'compte', 'espace', 'portail', 'mot de passe']) => [
                'La connexion donne accès à votre espace étudiant, parent, enseignant ou administrateur selon votre compte.',
                $this->links(['Se connecter' => route('login')]),
            ],
            Str::contains($question, ['bibliotheque', 'livre', 'document', 'ressource']) => [
                'La bibliothèque de l’INSG propose des ressources académiques et documentaires pour accompagner les étudiants et les enseignants.',
                $this->links(['Bibliothèque' => route('pages.bibliotheque')]),
            ],
            default => [
                'Je peux vous renseigner sur les formations, les admissions, les concours, les actualités, la bibliothèque, la connexion et les contacts de l’INSG. Que souhaitez-vous savoir ?',
                $this->links(['Voir les formations' => route('pages.formations'), 'Admissions' => route('pages.admissions'), 'Contact' => route('pages.contact')]),
            ],
        };

        return response()->json([
            'answer' => $answer,
            'links' => $links,
            'suggestions' => ['Quelles formations proposez-vous ?', 'Comment s’inscrire ?', 'Quels sont les concours en cours ?'],
        ]);
    }

    private function programsAnswer(): array
    {
        $programs = Program::where('active', true)->orderBy('sort_order')->pluck('name')->take(6);
        $list = $programs->isEmpty() ? 'plusieurs parcours en gestion et management' : $programs->join(', ', ' et ');

        return ["Les formations disponibles comprennent {$list}. Consultez la page Formations pour les niveaux, durées et débouchés.", $this->links(['Découvrir les formations' => route('pages.formations')])];
    }

    private function announcementsAnswer(): array
    {
        $announcement = Announcement::orderByDesc('published_at')->first();
        $message = $announcement
            ? "La dernière annonce publiée est « {$announcement->title} ». Consultez la page des annonces pour les détails et les échéances."
            : 'Aucune nouvelle annonce n’est publiée pour le moment.';

        return [$message, $this->links(['Annonces et concours' => route('pages.annonces-concours')])];
    }

    private function newsAnswer(): array
    {
        $article = Article::orderByDesc('published_at')->first();
        $message = $article ? "La dernière actualité est « {$article->title} »." : 'Retrouvez les dernières informations de l’INSG sur la page Actualités.';

        return [$message, $this->links(['Voir les actualités' => route('pages.actualites')])];
    }

    private function links(array $links): array
    {
        return collect($links)->map(fn ($url, $label) => compact('label', 'url'))->values()->all();
    }
}

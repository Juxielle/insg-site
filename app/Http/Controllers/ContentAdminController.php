<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Program;
use App\Models\SiteStatistic;
use App\Models\SiteSetting;
use App\Models\SiteMedia;
use App\Models\Testimonial;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentAdminController extends Controller
{
    public function dashboard(): View
    {
        $this->authorizeAdmin();

        return view('admin.content.dashboard', [
            'resources' => $this->resources(),
            'counts' => collect($this->resources())->mapWithKeys(
                fn (array $config, string $key) => [$key => $config['model']::count()]
            ),
        ]);
    }

    public function index(string $resource): View
    {
        $this->authorizeAdmin();
        $config = $this->resource($resource);

        return view('admin.content.index', [
            'resource' => $resource,
            'config' => $config,
            'resources' => $this->resources(),
            'items' => $config['model']::query()->orderByDesc('id')->paginate(15),
        ]);
    }

    public function create(string $resource): View
    {
        $this->authorizeAdmin();

        return $this->formView($resource, null);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $this->authorizeAdmin();
        $config = $this->resource($resource);
        $model = new $config['model'];
        $model->fill($this->validatedData($request, $resource, $config));
        $model->save();

        return redirect()->route('admin.content.index', $resource)
            ->with('backoffice_success', $config['singular'].' créé(e) avec succès.');
    }

    public function edit(string $resource, int $item): View
    {
        $this->authorizeAdmin();
        $config = $this->resource($resource);

        return $this->formView($resource, $config['model']::findOrFail($item));
    }

    public function update(Request $request, string $resource, int $item): RedirectResponse
    {
        $this->authorizeAdmin();
        $config = $this->resource($resource);
        $model = $config['model']::findOrFail($item);
        $model->fill($this->validatedData($request, $resource, $config, $model->getKey()));
        $model->save();

        return redirect()->route('admin.content.index', $resource)
            ->with('backoffice_success', $config['singular'].' mis(e) à jour.');
    }

    public function destroy(string $resource, int $item): RedirectResponse
    {
        $this->authorizeAdmin();
        $config = $this->resource($resource);

        try {
            $config['model']::findOrFail($item)->delete();
        } catch (QueryException) {
            return back()->withErrors('Cette information est utilisée ailleurs et ne peut pas être supprimée. Désactivez-la plutôt.');
        }

        return back()->with('backoffice_success', $config['singular'].' supprimé(e).');
    }

    private function formView(string $resource, mixed $item): View
    {
        return view('admin.content.form', [
            'resource' => $resource,
            'config' => $this->resource($resource),
            'resources' => $this->resources(),
            'item' => $item,
        ]);
    }

    private function validatedData(Request $request, string $resource, array $config, ?int $id = null): array
    {
        $rules = collect($config['fields'])->mapWithKeys(function (array $field, string $name) use ($id) {
            $rules = $field['rules'];
            if (($field['unique'] ?? false) === true) {
                $rules[] = Rule::unique($field['table'], $name)->ignore($id);
            }

            return [$name => $rules];
        })->all();

        foreach ($config['uploads'] ?? [] as $name) {
            $fieldIsRequired = in_array('required', $rules[$name], true);
            $hasCurrentImage = filled($request->input($name));
            $rules[$name] = ['nullable', 'string', 'max:255'];
            $rules[$name.'_file'] = [
                $fieldIsRequired && ! $hasCurrentImage ? 'required' : 'nullable',
                'image',
                'max:5120',
            ];
        }

        $data = $request->validate($rules);

        foreach ($config['uploads'] ?? [] as $name) {
            if ($request->hasFile($name.'_file')) {
                $directory = public_path('assets/uploads/'.$resource);
                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $filename = Str::uuid().'.'.$request->file($name.'_file')->extension();
                $request->file($name.'_file')->move($directory, $filename);
                $data[$name] = '/assets/uploads/'.$resource.'/'.$filename;
            }
            unset($data[$name.'_file']);
        }

        foreach ($config['booleans'] ?? [] as $name) {
            $data[$name] = $request->boolean($name);
        }
        foreach ($config['arrays'] ?? [] as $name) {
            $data[$name] = collect(preg_split('/\r\n|\r|\n/', (string) ($data[$name] ?? '')))
                ->map(fn (string $value) => trim($value))->filter()->values()->all();
        }
        foreach ($config['jsons'] ?? [] as $name) {
            $data[$name] = filled($data[$name] ?? null) ? json_decode($data[$name], true, 512, JSON_THROW_ON_ERROR) : null;
        }
        if (array_key_exists('slug', $config['fields']) && blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name'] ?? $data['title']);
        }

        return Arr::only($data, array_keys($config['fields']));
    }

    private function resource(string $resource): array
    {
        abort_unless(array_key_exists($resource, $this->resources()), 404);

        return $this->resources()[$resource];
    }

    private function resources(): array
    {
        $text = fn (string $label, bool $required = true) => ['label' => $label, 'type' => 'text', 'rules' => [$required ? 'required' : 'nullable', 'string', 'max:255']];
        $textarea = fn (string $label, bool $required = true) => ['label' => $label, 'type' => 'textarea', 'rules' => [$required ? 'required' : 'nullable', 'string']];
        $checkbox = fn (string $label) => ['label' => $label, 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']];

        return [
            'pages' => [
                'label' => 'Pages du site', 'singular' => 'Page', 'icon' => 'bi-file-earmark-richtext', 'model' => Page::class,
                'list' => ['name' => 'Page', 'hero_title' => 'Titre public', 'slug' => 'Identifiant', 'active' => 'Publiée'],
                'fields' => [
                    'name' => $text('Nom dans le back-office'),
                    'slug' => $text('Identifiant de la page') + ['unique' => true, 'table' => 'pages'],
                    'meta_title' => $text('Titre SEO'), 'meta_description' => $textarea('Description SEO', false),
                    'hero_title' => $text('Titre principal'), 'hero_text' => $textarea('Texte d’introduction', false),
                    'active' => $checkbox('Page publiée'),
                ], 'booleans' => ['active'],
            ],
            'page-sections' => [
                'label' => 'Sections des pages', 'singular' => 'Section', 'icon' => 'bi-layout-text-window-reverse', 'model' => PageSection::class,
                'list' => ['page.name' => 'Page', 'name' => 'Nom interne', 'type' => 'Type', 'sort_order' => 'Ordre', 'active' => 'Publiée'],
                'fields' => [
                    'page_id' => ['label' => 'Page', 'type' => 'select', 'options' => Page::orderBy('name')->pluck('name', 'id')->all(), 'rules' => ['required', 'exists:pages,id']],
                    'key' => $text('Clé technique'), 'name' => $text('Nom dans le back-office'),
                    'type' => ['label' => 'Type de section', 'type' => 'select', 'options' => [
                        'rich_text' => 'Texte éditorial', 'cards' => 'Liste de cartes', 'cta' => 'Appel à l’action',
                        'programs' => 'Catalogue des formations', 'articles' => 'Liste des actualités',
                        'announcements' => 'Liste des annonces', 'events' => 'Liste des événements',
                        'partners' => 'Liste des partenaires', 'testimonials' => 'Liste des témoignages',
                        'statistics' => 'Liste des chiffres clés', 'contact_form' => 'Formulaire de contact',
                        'admission_form' => 'Formulaire de pré-inscription', 'master_form' => 'Formulaire Master',
                    ], 'rules' => ['required', Rule::in(['rich_text', 'cards', 'cta', 'programs', 'articles', 'announcements', 'events', 'partners', 'testimonials', 'statistics', 'contact_form', 'admission_form', 'master_form'])]],
                    'eyebrow' => $text('Sur-titre', false), 'title' => $text('Titre', false), 'body' => $textarea('Contenu', false),
                    'image_url' => $text('Image de la section', false) + ['upload' => true],
                    'items' => ['label' => 'Cartes au format JSON', 'type' => 'textarea', 'rules' => ['nullable', 'json']],
                    'button_label' => $text('Libellé du bouton', false), 'button_url' => $text('Lien du bouton', false),
                    'theme' => ['label' => 'Fond', 'type' => 'select', 'options' => ['light' => 'Blanc', 'gray' => 'Gris clair', 'navy' => 'Bleu institutionnel'], 'rules' => ['required', Rule::in(['light', 'gray', 'navy'])]],
                    'sort_order' => ['label' => 'Ordre', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    'active' => $checkbox('Section publiée'),
                ], 'booleans' => ['active'], 'uploads' => ['image_url'], 'jsons' => ['items'],
            ],
            'programs' => [
                'label' => 'Formations', 'singular' => 'Formation', 'icon' => 'bi-mortarboard', 'model' => Program::class,
                'list' => ['name' => 'Nom', 'level' => 'Niveau', 'duration' => 'Durée', 'active' => 'Active'],
                'fields' => [
                    'name' => $text('Nom'), 'slug' => $text('Slug', false) + ['unique' => true, 'table' => 'programs'],
                    'category' => $text('Catégorie'), 'level' => $text('Niveau'), 'duration' => $text('Durée', false),
                    'icon' => $text('Icône Bootstrap', false), 'description' => $textarea('Description'),
                    'opportunities' => $textarea('Débouchés (un par ligne)', false), 'curriculum' => $textarea('Programme (un élément par ligne)', false),
                    'featured' => $checkbox('Afficher sur l’accueil'), 'active' => $checkbox('Formation active'),
                    'sort_order' => ['label' => 'Ordre', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ], 'booleans' => ['featured', 'active'], 'arrays' => ['opportunities', 'curriculum'],
            ],
            'articles' => [
                'label' => 'Actualités', 'singular' => 'Actualité', 'icon' => 'bi-newspaper', 'model' => Article::class,
                'list' => ['title' => 'Titre', 'category' => 'Catégorie', 'published_at' => 'Publication', 'featured' => 'À la une'],
                'fields' => [
                    'title' => $text('Titre'), 'slug' => $text('Slug', false) + ['unique' => true, 'table' => 'articles'],
                    'category' => $text('Catégorie'), 'excerpt' => $textarea('Résumé'), 'content' => $textarea('Contenu', false),
                    'image_url' => $text('Image de l’actualité', false) + ['upload' => true],
                    'published_at' => ['label' => 'Date de publication', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
                    'featured' => $checkbox('À la une'),
                ], 'booleans' => ['featured'], 'uploads' => ['image_url'],
            ],
            'events' => [
                'label' => 'Événements', 'singular' => 'Événement', 'icon' => 'bi-calendar-event', 'model' => Event::class,
                'list' => ['title' => 'Titre', 'location' => 'Lieu', 'starts_at' => 'Début', 'ends_at' => 'Fin'],
                'fields' => [
                    'title' => $text('Titre'), 'description' => $textarea('Description', false), 'location' => $text('Lieu'),
                    'starts_at' => ['label' => 'Début', 'type' => 'datetime-local', 'rules' => ['required', 'date']],
                    'ends_at' => ['label' => 'Fin', 'type' => 'datetime-local', 'rules' => ['nullable', 'date', 'after_or_equal:starts_at']],
                ],
            ],
            'announcements' => [
                'label' => 'Annonces et concours', 'singular' => 'Annonce', 'icon' => 'bi-megaphone', 'model' => Announcement::class,
                'list' => ['title' => 'Titre', 'type' => 'Type', 'published_at' => 'Publication', 'status' => 'Statut'],
                'fields' => [
                    'title' => $text('Titre'), 'type' => $text('Type'), 'description' => $textarea('Description'),
                    'published_at' => ['label' => 'Date de publication', 'type' => 'date', 'rules' => ['required', 'date']],
                    'deadline_at' => ['label' => 'Date limite', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    'status' => ['label' => 'Statut', 'type' => 'select', 'options' => ['open' => 'Ouverte', 'published' => 'Publiée', 'closed' => 'Fermée'], 'rules' => ['required', Rule::in(['open', 'published', 'closed'])]],
                ],
            ],
            'partners' => [
                'label' => 'Partenaires', 'singular' => 'Partenaire', 'icon' => 'bi-building', 'model' => Partner::class,
                'list' => ['name' => 'Nom', 'category' => 'Catégorie', 'website' => 'Site web', 'active' => 'Actif'],
                'fields' => [
                    'name' => $text('Nom'), 'category' => $text('Catégorie', false),
                    'logo_url' => $text('Logo du partenaire', false) + ['upload' => true],
                    'website' => ['label' => 'Site web', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:255']],
                    'active' => $checkbox('Partenaire actif'),
                ], 'booleans' => ['active'], 'uploads' => ['logo_url'],
            ],
            'testimonials' => [
                'label' => 'Témoignages', 'singular' => 'Témoignage', 'icon' => 'bi-chat-quote', 'model' => Testimonial::class,
                'list' => ['name' => 'Nom', 'role' => 'Fonction', 'featured' => 'Publié'],
                'fields' => [
                    'name' => $text('Nom'), 'role' => $text('Fonction / promotion'), 'quote' => $textarea('Témoignage'),
                    'avatar_url' => $text('Portrait', false) + ['upload' => true],
                    'featured' => $checkbox('Publier sur le site'),
                ], 'booleans' => ['featured'], 'uploads' => ['avatar_url'],
            ],
            'statistics' => [
                'label' => 'Chiffres clés', 'singular' => 'Chiffre clé', 'icon' => 'bi-bar-chart', 'model' => SiteStatistic::class,
                'list' => ['label' => 'Libellé', 'value' => 'Valeur', 'suffix' => 'Suffixe', 'sort_order' => 'Ordre'],
                'fields' => [
                    'key' => $text('Clé technique') + ['unique' => true, 'table' => 'site_statistics'], 'label' => $text('Libellé'),
                    'value' => ['label' => 'Valeur', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    'suffix' => $text('Suffixe', false), 'sort_order' => ['label' => 'Ordre', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ],
            ],
            'settings' => [
                'label' => 'Informations générales', 'singular' => 'Information générale', 'icon' => 'bi-gear', 'model' => SiteSetting::class,
                'list' => ['label' => 'Information', 'value' => 'Valeur', 'key' => 'Clé'],
                'fields' => [
                    'key' => $text('Clé technique') + ['unique' => true, 'table' => 'site_settings'],
                    'label' => $text('Libellé'), 'value' => $textarea('Valeur', false),
                    'type' => ['label' => 'Type', 'type' => 'select', 'options' => ['text' => 'Texte', 'textarea' => 'Texte long', 'email' => 'Email', 'url' => 'Lien'], 'rules' => ['required', Rule::in(['text', 'textarea', 'email', 'url'])]],
                ],
            ],
            'media' => [
                'label' => 'Médiathèque', 'singular' => 'Image', 'icon' => 'bi-images', 'model' => SiteMedia::class,
                'list' => ['label' => 'Emplacement', 'image_url' => 'Image', 'alt_text' => 'Texte alternatif'],
                'fields' => [
                    'key' => $text('Clé technique') + ['unique' => true, 'table' => 'site_media'],
                    'label' => $text('Emplacement sur le site'),
                    'image_url' => $text('Image') + ['upload' => true],
                    'alt_text' => $text('Description accessible', false),
                ], 'uploads' => ['image_url'],
            ],
        ];
    }

    private function authorizeAdmin(): void
    {
        abort_unless(request()->user()?->role === 'admin', 403);
    }
}

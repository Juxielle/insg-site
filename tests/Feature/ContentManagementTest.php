<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\SiteMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_content_management_requires_an_authenticated_admin(): void
    {
        $this->get(route('admin.content.dashboard'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('admin.content.dashboard'))->assertForbidden();
    }

    public function test_admin_can_manage_an_article_and_frontend_reflects_changes(): void
    {
        $this->actingAs(User::where('role', 'admin')->firstOrFail());

        $this->get(route('admin.content.index', 'articles'))
            ->assertOk()->assertSee('Actualités');

        $this->post(route('admin.content.store', 'articles'), [
            'title' => 'Nouvelle actualité administrable',
            'slug' => 'nouvelle-actualite-administrable',
            'category' => 'Campus',
            'excerpt' => 'Résumé publié depuis le back-office.',
            'content' => 'Contenu complet.',
            'image_url' => '/assets/images/insg2.jpg',
            'published_at' => '2026-08-30T12:00',
            'featured' => '1',
        ])->assertRedirect(route('admin.content.index', 'articles'));

        $article = Article::where('slug', 'nouvelle-actualite-administrable')->firstOrFail();
        $this->get('/')->assertOk()->assertSee($article->title);
        $this->get('/pages/actualites.html')->assertOk()->assertSee($article->title);

        $this->put(route('admin.content.update', ['articles', $article->id]), [
            'title' => 'Actualité mise à jour',
            'slug' => $article->slug,
            'category' => 'Campus',
            'excerpt' => 'Résumé modifié.',
            'content' => 'Contenu complet.',
            'image_url' => $article->image_url,
            'published_at' => '2026-08-30T12:00',
            'featured' => '0',
        ])->assertRedirect(route('admin.content.index', 'articles'));

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'Actualité mise à jour', 'featured' => false]);

        $this->delete(route('admin.content.destroy', ['articles', $article->id]))
            ->assertSessionHas('backoffice_success');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_content_seeder_is_idempotent(): void
    {
        $before = Article::count();
        $this->seed(\Database\Seeders\ContentSeeder::class);
        $this->assertSame($before, Article::count());
    }

    public function test_admin_can_update_page_content_visible_on_the_frontend(): void
    {
        $this->actingAs(User::where('role', 'admin')->firstOrFail());
        $page = Page::where('slug', 'about')->firstOrFail();

        $this->put(route('admin.content.update', ['pages', $page->id]), [
            'name' => $page->name,
            'slug' => $page->slug,
            'meta_title' => 'Présentation institutionnelle',
            'meta_description' => 'Description administrable.',
            'hero_title' => 'Titre administrable de la page',
            'hero_text' => 'Introduction administrable depuis le CMS.',
            'active' => '1',
        ])->assertRedirect(route('admin.content.index', 'pages'));

        $this->get('/pages/about.html')->assertOk()
            ->assertSee('Présentation institutionnelle')
            ->assertSee('Titre administrable de la page')
            ->assertSee('Introduction administrable depuis le CMS.');
    }

    public function test_admin_can_update_an_internal_section_and_global_setting(): void
    {
        $this->actingAs(User::where('role', 'admin')->firstOrFail());
        $section = PageSection::where('key', 'history')->firstOrFail();

        $this->put(route('admin.content.update', ['page-sections', $section->id]), [
            'page_id' => $section->page_id, 'key' => $section->key, 'name' => $section->name,
            'type' => 'rich_text', 'eyebrow' => 'Institution', 'title' => 'Section interne administrable',
            'body' => '<p>Ce texte provient intégralement du backend.</p>', 'image_url' => $section->image_url,
            'theme' => 'light', 'sort_order' => 10, 'active' => '1',
        ])->assertRedirect(route('admin.content.index', 'page-sections'));

        $setting = SiteSetting::where('key', 'footer_description')->firstOrFail();
        $this->put(route('admin.content.update', ['settings', $setting->id]), [
            'key' => $setting->key, 'label' => $setting->label,
            'value' => 'Pied de page administrable.', 'type' => 'textarea',
        ])->assertRedirect(route('admin.content.index', 'settings'));

        $this->get('/pages/about.html')->assertOk()
            ->assertSee('Section interne administrable')
            ->assertSee('Ce texte provient intégralement du backend.', false)
            ->assertSee('Pied de page administrable.');
    }

    public function test_admin_can_change_a_global_frontend_image(): void
    {
        $this->actingAs(User::where('role', 'admin')->firstOrFail());
        $media = SiteMedia::where('key', 'site_logo')->firstOrFail();

        $this->get(route('admin.content.edit', ['media', $media->id]))
            ->assertOk()
            ->assertSee('Remplacer l’image')
            ->assertSee('data-image-input', false)
            ->assertDontSee('type="text" id="image_url"', false);

        $this->put(route('admin.content.update', ['media', $media->id]), [
            'key' => $media->key,
            'label' => $media->label,
            'image_url' => '/assets/images/insg5.jpg',
            'alt_text' => 'Nouveau logo du site',
        ])->assertRedirect(route('admin.content.index', 'media'));

        $this->assertDatabaseHas('site_media', [
            'key' => 'site_logo',
            'image_url' => '/assets/images/insg5.jpg',
        ]);
        $this->get('/')->assertOk()->assertSee('/assets/images/insg5.jpg', false);
        $this->get('/pages/about.html')->assertOk()->assertSee('/assets/images/insg5.jpg', false);
    }
}

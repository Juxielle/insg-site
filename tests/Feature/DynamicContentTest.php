<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_seeders_create_institutional_content_and_admin_access(): void
    {
        $this->assertDatabaseCount('programs', 8);
        $this->assertDatabaseCount('articles', 6);
        $this->assertDatabaseCount('partners', 38);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => 'admin@insg.ga', 'role' => 'admin']);
    }

    public function test_public_pages_render_database_content(): void
    {
        $this->get('/')->assertOk()->assertSee(Program::first()->name)->assertSee(Article::first()->title);
        $this->assertCount(6, $this->get('/')->viewData('partners'));
        $this->get('/pages/formations.html')->assertOk()->assertSee('Informatique de Gestion');
        $this->get('/pages/actualites.html')->assertOk()->assertSee(Article::latest('published_at')->first()->title);
    }

    public function test_admin_content_dashboard_renders(): void
    {
        $this->actingAs(User::where('role', 'admin')->first())
            ->get(route('admin.content.dashboard'))->assertOk()->assertSee('Gestion des contenus');
    }

    public function test_public_navigation_marks_current_page_and_parent_menu_as_active(): void
    {
        $this->get('/pages/about.html')->assertOk()
            ->assertSee('nav-link active', false);

        $this->get('/pages/formations.html')->assertOk()
            ->assertSee('dropdown-toggle active', false)
            ->assertSee('dropdown-item active', false);
    }
}

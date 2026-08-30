<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guests_are_redirected_to_login_from_the_backoffice(): void
    {
        $this->get(route('admin.content.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_is_redirected_to_content_management_after_login(): void
    {
        $user = User::where('role', 'admin')->firstOrFail();
        $this->post('/connexion', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.content.dashboard'));
    }

    public function test_non_admin_accounts_cannot_access_the_backend(): void
    {
        $user = User::factory()->create(['role' => 'student', 'password' => 'password']);
        $this->post('/connexion', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_navigation_contains_administration_access(): void
    {
        $this->get('/')->assertOk()->assertSee('Administration')->assertSee(route('admin.login'));
    }

    public function test_administration_button_forces_a_fresh_login_even_with_an_active_session(): void
    {
        $this->actingAs(User::where('role', 'admin')->firstOrFail())
            ->get(route('admin.login'))->assertOk()
            ->assertSee('Se connecter');

        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_template_pages_are_available(): void
    {
        $urls = [
            '/', '/pages/about.html', '/pages/actualites.html', '/pages/admissions.html',
            '/pages/annonces-concours.html', '/pages/bibliotheque.html', '/pages/contact.html',
            '/pages/entreprises.html', '/pages/formations.html', '/pages/incubateur.html',
            '/pages/inscription-master.html', '/pages/recherche.html', '/pages/vie-etudiante.html',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_contact_message_is_validated_and_stored(): void
    {
        $response = $this->post('/contact', [
            'nom' => 'Jean Test',
            'email' => 'jean@example.com',
            'telephone' => '+241 00 00 00 00',
            'sujet' => 'Admissions',
            'message' => 'Bonjour, je souhaite obtenir des informations.',
        ]);

        $response->assertSessionHas('success', 'contact');
        $this->assertDatabaseHas('submissions', [
            'type' => 'contact',
            'email' => 'jean@example.com',
        ]);
    }

    public function test_invalid_contact_message_is_rejected(): void
    {
        $this->post('/contact', ['email' => 'incorrect'])
            ->assertSessionHasErrors(['nom', 'email', 'sujet', 'message']);
    }
}

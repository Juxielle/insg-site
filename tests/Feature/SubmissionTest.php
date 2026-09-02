<?php

namespace Tests\Feature;

use App\Models\Submission;
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

    public function test_master_candidate_can_track_their_application(): void
    {
        $submission = Submission::create([
            'type' => 'master',
            'tracking_number' => 'MASTER-'.date('Y').'-000001',
            'name' => 'Aline Test',
            'email' => 'aline@example.com',
            'data' => ['niveau' => 'Master 1', 'specialite' => 'Finance'],
            'status' => 'pending',
        ]);

        $this->get(route('master.tracking'))->assertOk()->assertSee('Suivre ma candidature en Master');
        $this->post(route('master.track'), [
            'tracking_number' => strtolower($submission->tracking_number),
            'email' => $submission->email,
        ])->assertOk()->assertSee($submission->tracking_number)->assertSee('En cours d’examen');

        $this->post(route('master.track'), [
            'tracking_number' => $submission->tracking_number,
            'email' => 'autre@example.com',
        ])->assertOk()->assertSee('Aucun dossier ne correspond');
    }
}

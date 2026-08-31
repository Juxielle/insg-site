<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('local');
    }

    public function test_contact_form_is_saved_and_visible_to_admin(): void
    {
        $this->post(route('contact.store'), [
            'nom' => 'Jeanne Test', 'email' => 'jeanne@example.com', 'telephone' => '060000000',
            'sujet' => 'Demande d’information', 'message' => 'Bonjour, je souhaite recevoir des informations.',
        ])->assertRedirect()->assertSessionHas('success', 'contact');

        $submission = Submission::where('type', 'contact')->firstOrFail();
        $this->actingAs($this->admin())->get(route('admin.content.submissions.show', $submission))
            ->assertOk()->assertSee('Jeanne Test')->assertSee('Demande d’information');
    }

    public function test_contest_registration_with_document_can_be_approved(): void
    {
        $this->post(route('admissions.store'), [
            'nom' => 'Mba', 'prenom' => 'Paul', 'date_naissance' => '2002-05-10',
            'telephone' => '061111111', 'email' => 'paul@example.com', 'filiere' => 'Finance',
            'niveau' => 'Licence 1', 'documents' => [UploadedFile::fake()->create('bac.pdf', 100, 'application/pdf')],
            'consentement' => '1',
        ])->assertRedirect()->assertSessionHas('success', 'admission');

        $submission = Submission::where('type', 'admission')->firstOrFail();
        Storage::disk('local')->assertExists($submission->documents[0]['path']);
        $this->actingAs($this->admin())->put(route('admin.content.submissions.status', $submission), [
            'status' => 'approved', 'admin_note' => 'Dossier complet.',
        ])->assertRedirect();
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'status' => 'approved', 'admin_note' => 'Dossier complet.']);
    }

    public function test_master_registration_and_all_documents_are_saved(): void
    {
        $pdf = fn (string $name) => UploadedFile::fake()->create($name, 100, 'application/pdf');
        $this->post(route('master.store'), [
            'nom' => 'Ndong', 'prenom' => 'Alice', 'date_naissance' => '2000-01-15', 'nationalite' => 'Gabonaise',
            'telephone' => '062222222', 'email' => 'alice@example.com', 'residence' => 'Libreville',
            'specialite' => 'Finance', 'niveau' => 'Master 1', 'dernier_diplome' => 'Licence',
            'etablissement' => 'INSG', 'annee_obtention' => 2025, 'mention' => 'Bien', 'moyenne' => 14,
            'projet_professionnel' => str_repeat('Je souhaite développer mes compétences en finance et contribuer aux entreprises gabonaises. ', 2),
            'diplome' => $pdf('diplome.pdf'), 'releves_notes' => [$pdf('releve.pdf')], 'cv' => $pdf('cv.pdf'),
            'lettre_motivation' => $pdf('lettre.pdf'), 'piece_identite' => $pdf('identite.pdf'),
            'declaration' => '1', 'consentement' => '1',
        ])->assertRedirect()->assertSessionHas('success', 'master');

        $submission = Submission::where('type', 'master')->firstOrFail();
        $this->assertCount(5, $submission->documents);
        $this->actingAs($this->admin())->get(route('admin.content.submissions.show', $submission))
            ->assertOk()->assertSee('Alice Ndong')->assertSee('diplome.pdf');
    }

    public function test_submission_management_is_restricted_to_admins(): void
    {
        $this->get(route('admin.content.submissions.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('admin.content.submissions.index'))->assertForbidden();
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }
}

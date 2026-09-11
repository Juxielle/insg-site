<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\User;
use App\Services\ContestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContestModuleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User { return User::factory()->create(['role' => 'admin']); }
    private function payload(): array
    {
        return ['title' => 'Concours test', 'description' => 'Concours INSG', 'academic_year' => '2027-2028', 'session' => 'Juin', 'type' => 'Entrée', 'registration_starts_at' => '2027-01-01T08:00', 'registration_ends_at' => '2027-05-31T18:00', 'exam_date' => '2027-06-15', 'exam_time' => '08:00', 'location' => 'INSG', 'available_places' => 100];
    }
    private function contest(User $admin): Contest { return app(ContestService::class)->createContest($this->payload(), $admin); }
    private function entry(array $overrides = []): array { return array_replace(['registration_number' => 'TEST-2027', 'last_name' => 'MBA', 'first_names' => 'Alice', 'field' => 'Gestion', 'average' => 12.75, 'decision' => 'Admis(e)', 'rank' => 7], $overrides); }
    private function save(Contest $contest, int $round, array $entries, bool $publish = false)
    {
        return $this->put(route('admin.contests.results.save', $contest), ['round' => $round, 'entries' => $entries, 'publish' => $publish ? '1' : '0']);
    }

    public function test_creation_has_two_rounds_without_tracks_or_subjects(): void
    {
        $this->actingAs($this->admin())->post(route('admin.contests.store'), $this->payload())->assertRedirect()->assertSessionHasNoErrors();
        $contest = Contest::firstOrFail();
        $this->assertCount(2, $contest->rounds);
        $this->assertDatabaseCount('contest_tracks', 0);
        $this->assertDatabaseCount('contest_subjects', 0);
        $this->get(route('admin.contests.show', $contest))->assertOk()->assertSee('Premier tour')->assertSee('Deuxième tour')->assertDontSee('Filières et matières');
        $this->get(route('admin.contests.results', $contest))->assertOk()->assertSee('Ajouter un étudiant');
    }

    public function test_rounds_publish_independently_and_preserve_manual_results(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 1, [$this->entry()], true)->assertSessionHasNoErrors();
        $this->save($contest, 2, [$this->entry(['last_name' => 'SECOND', 'average' => 18, 'rank' => 2])])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('contest_entries', ['contest_id' => $contest->id, 'round' => 1, 'rank' => 7, 'decision' => 'Admis(e)', 'average' => 12.75]);
        $this->post(route('contests.results.search'), ['registration_number' => 'TEST-2027', 'verification_code' => '2027'])->assertOk()->assertSee('MBA')->assertDontSee('12,75')->assertDontSee('Moyenne')->assertDontSee('SECOND');
        $this->get(route('contests.results', ['contest_id' => $contest->id, 'round' => 2]))->assertOk()->assertDontSee('SECOND');
        $this->post(route('admin.contests.publish', $contest), ['round' => 2])->assertSessionHasNoErrors();
        $this->post(route('contests.results.search'), ['registration_number' => 'TEST-2027', 'verification_code' => '2027'])->assertOk()->assertSee('SECOND')->assertDontSee('MBA');
        $this->get(route('admin.contests.export', [$contest, 'round' => 2]))->assertOk()->assertStreamedContent("\xEF\xBB\xBFMatricule;\"Date de naissance\";Nom;Prénom;Filière;Moyenne;Décision;Rang\nTEST-2027;;SECOND;Alice;Gestion;18.00;Admis(e);2\n");
    }

    public function test_second_round_cannot_publish_before_first_and_failure_is_atomic(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 2, [$this->entry()], true)->assertSessionHasErrors('round');
        $this->assertDatabaseCount('contest_entries', 0);
        $this->post(route('admin.contests.publish', $contest), ['round' => 1])->assertSessionHasErrors('entries');
    }

    public function test_published_round_is_locked_and_unpublishing_keeps_other_round_visible(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 1, [$this->entry()], true)->assertSessionHasNoErrors();
        $this->save($contest, 1, [$this->entry(['last_name' => 'CHANGED'])])->assertSessionHasErrors('entries');
        $this->save($contest, 2, [$this->entry()], true)->assertSessionHasNoErrors();
        $this->post(route('admin.contests.unpublish', $contest), ['round' => 1])->assertSessionHasErrors('round');
        $this->post(route('admin.contests.unpublish', $contest), ['round' => 2])->assertSessionHasNoErrors();
        $this->assertSame('results_published', $contest->fresh()->status);
        $this->post(route('admin.contests.unpublish', $contest), ['round' => 1])->assertSessionHasNoErrors();
        $this->get(route('contests.results', ['contest_id' => $contest->id, 'round' => 1]))->assertDontSee('MBA');
        $this->save($contest, 1, [$this->entry(['last_name' => 'CHANGED'])])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('contest_entries', ['round' => 1, 'last_name' => 'CHANGED']);
    }

    public function test_validation_rejects_incomplete_results_and_invalid_rounds(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 1, [$this->entry(['field' => '', 'average' => 21, 'rank' => 0, 'decision' => ''])])->assertSessionHasErrors(['entries.0.field', 'entries.0.average', 'entries.0.rank', 'entries.0.decision']);
        $this->save($contest, 3, [$this->entry()])->assertSessionHasErrors('round');
        $this->assertDatabaseCount('contest_entries', 0);
    }

    public function test_contests_are_isolated_and_search_never_leaks_drafts(): void
    {
        $admin = $this->admin(); $first = $this->contest($admin); $second = $this->contest($admin); $this->actingAs($admin);
        $this->save($first, 1, [$this->entry()], true);
        $this->save($second, 1, [$this->entry(['last_name' => 'SECRET'])]);
        $this->post(route('contests.results.search'), ['registration_number' => 'SECRET', 'verification_code' => '2027'])->assertOk()->assertDontSee('Alice');
        $this->post(route('contests.results.search'), ['registration_number' => 'Absent', 'verification_code' => '2027'])->assertOk()->assertDontSee('Alice');
        $this->assertDatabaseHas('contest_entries', ['contest_id' => $first->id, 'last_name' => 'MBA']);
    }

    public function test_non_admin_cannot_manage_results(): void
    {
        $contest = $this->contest($this->admin());
        $this->get(route('admin.contests.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'student']));
        $this->get(route('admin.contests.results', $contest))->assertForbidden();
        $this->save($contest, 1, [$this->entry()], true)->assertForbidden();
        $this->post(route('admin.contests.publish', $contest), ['round' => 1])->assertForbidden();
    }

    public function test_seeded_contests_use_the_new_publication_flow(): void
    {
        $this->seed();
        $this->get(route('home'))->assertOk();
        $this->get(route('contests.index'))->assertOk();
        $this->post(route('contests.results.search'), ['registration_number' => 'INSG-2026-0001', 'verification_code' => '2026'])->assertOk()->assertSee('OBIANG');
        $this->assertDatabaseCount('contest_tracks', 0);
    }

    public function test_bulk_json_input_and_unexpected_fields_are_validated(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->put(route('admin.contests.results.save', $contest), ['round' => 1, 'entries_json' => json_encode(array_fill(0, 200, $this->entry()))])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('contest_entries', 200);
        $this->save($contest, 1, [$this->entry(['contest_id' => 999, 'round' => 2])])->assertSessionHasErrors('entries.0');
        $this->assertDatabaseCount('contest_entries', 200);
    }

    public function test_migration_preserves_existing_candidates_and_published_results(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin);
        $contest->update(['status' => 'results_published', 'published_at' => now()]);
        $candidate = \App\Models\Candidate::create([
            'last_name' => 'HISTORIQUE', 'first_names' => 'Alice', 'birth_date' => '2000-01-01',
            'nationality' => 'Gabonaise', 'phone' => '060000000', 'email' => 'history@example.test', 'study_level' => 'Bac', 'diploma' => 'Bac',
        ]);
        $track = $contest->tracks()->create(['name' => 'Gestion', 'sort_order' => 1]);
        $application = $contest->applications()->create(['candidate_id' => $candidate->id, 'contest_track_id' => $track->id, 'status' => 'validated', 'source' => 'admin', 'verification_code' => 'legacy-code', 'submitted_at' => now()]);
        $application->result()->create(['average' => 15, 'decision' => 'admitted', 'rank' => 3]);
        $migration = require database_path('migrations/2026_09_09_000000_create_contest_round_entries.php');
        $migration->down();
        $migration->up();
        (require database_path('migrations/2026_09_09_010000_add_registration_number_to_contest_entries.php'))->up();
        (require database_path('migrations/2026_09_10_000000_add_contest_birth_date.php'))->up();
        $this->assertDatabaseHas('contest_entries', ['contest_id' => $contest->id, 'round' => 1, 'last_name' => 'HISTORIQUE', 'field' => 'Gestion', 'average' => 15, 'decision' => 'Admis(e)', 'rank' => 3]);
        $this->assertNotNull($contest->rounds()->where('number', 1)->first()->published_at);
        $this->assertNull($contest->rounds()->where('number', 2)->first()->published_at);
        $contest->entries()->update(['registration_number' => 'LEGACY-1']);
        $this->post(route('contests.results.search'), ['registration_number' => 'LEGACY-1', 'verification_code' => '2027'])->assertOk()->assertSee('HISTORIQUE');
    }

    public function test_students_can_be_added_edited_and_deleted_in_the_selected_round(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->get(route('admin.contests.entries.create', [$contest, 2]))->assertOk()->assertSee('Ajouter un étudiant manuellement');
        $this->post(route('admin.contests.entries.store', [$contest, 2]), $this->entry())->assertSessionHasNoErrors();
        $entry = $contest->entries()->firstOrFail();
        $this->assertSame(2, $entry->round);
        $this->get(route('admin.contests.entries.edit', [$contest, 2, $entry]))->assertOk()->assertSee('Alice');
        $this->put(route('admin.contests.entries.update', [$contest, 2, $entry]), $this->entry(['average' => 18, 'decision' => 'Admis(e)']))->assertSessionHasNoErrors();
        $this->assertSame('18.00', $entry->fresh()->average);
        $this->assertSame('Admis(e)', $entry->fresh()->decision);
        $this->delete(route('admin.contests.entries.destroy', [$contest, 2, $entry]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('contest_entries', 0);
    }

    public function test_entry_mutations_and_clear_are_scoped_to_the_contest_and_round(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $other = $this->contest($admin); $this->actingAs($admin);
        $entry = $contest->entries()->create($this->entry() + ['round' => 1]);
        $second = $contest->entries()->create($this->entry() + ['round' => 2]);
        $foreign = $other->entries()->create($this->entry() + ['round' => 1]);
        $this->put(route('admin.contests.entries.update', [$contest, 2, $entry]), $this->entry())->assertNotFound();
        $this->delete(route('admin.contests.entries.destroy', [$contest, 1, $foreign]))->assertNotFound();
        $this->delete(route('admin.contests.entries.clear', [$contest, 1]))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('contest_entries', ['id' => $entry->id]);
        $this->assertDatabaseHas('contest_entries', ['id' => $second->id]);
        $this->assertDatabaseHas('contest_entries', ['id' => $foreign->id]);
    }

    public function test_excel_import_appends_all_rows_to_only_the_selected_round(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $existing = $contest->entries()->create($this->entry() + ['round' => 2]);
        $file = new \Illuminate\Http\UploadedFile(base_path('tests/Fixtures/contest-entries.xlsx'), 'etudiants.xlsx', null, null, true);
        $this->post(route('admin.contests.entries.import', [$contest, 2]), ['excel_file' => $file])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('contest_entries', 3);
        $this->assertDatabaseHas('contest_entries', ['id' => $existing->id]);
        $this->assertDatabaseHas('contest_entries', ['contest_id' => $contest->id, 'round' => 2, 'last_name' => 'ÉTUDIANTE', 'average' => 14.5, 'rank' => 1]);
        $this->assertSame(0, $contest->entries()->where('round', 1)->count());
    }

    public function test_invalid_excel_import_does_not_insert_any_rows(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $file = new \Illuminate\Http\UploadedFile(base_path('tests/Fixtures/contest-entries-invalid.xlsx'), 'etudiants.xlsx', null, null, true);
        $this->post(route('admin.contests.entries.import', [$contest, 1]), ['excel_file' => $file])->assertSessionHasErrors('excel_file');
        $this->assertDatabaseCount('contest_entries', 0);
        $this->post(route('admin.contests.entries.import', [$contest, 1]), ['excel_file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('faux.xlsx', 'invalid')])->assertSessionHasErrors('excel_file');
    }

    public function test_published_round_rejects_all_list_mutations(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 1, [$this->entry()], true)->assertSessionHasNoErrors();
        $entry = $contest->entries()->firstOrFail();
        $this->post(route('admin.contests.entries.store', [$contest, 1]), $this->entry())->assertSessionHasErrors('round');
        $this->put(route('admin.contests.entries.update', [$contest, 1, $entry]), $this->entry())->assertSessionHasErrors('round');
        $this->delete(route('admin.contests.entries.destroy', [$contest, 1, $entry]))->assertSessionHasErrors('round');
        $this->delete(route('admin.contests.entries.clear', [$contest, 1]))->assertSessionHasErrors('round');
        $this->post(route('admin.contests.entries.import', [$contest, 1]))->assertSessionHasErrors('round');
        $this->assertDatabaseCount('contest_entries', 1);
    }

    public function test_pdf_download_and_action_buttons_are_available_for_each_round(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $contest->entries()->create($this->entry() + ['round' => 2]);
        $this->get(route('admin.contests.results', [$contest, 'round' => 2]))->assertOk()->assertSee('Importer un fichier Excel')->assertSee('Vider la liste du tour 2')->assertSee('Modifier')->assertSee('Supprimer')->assertSee('Télécharger les résultats en PDF');
        $response = $this->get(route('admin.contests.pdf', [$contest, 2]))->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertHeader('Content-Disposition', 'attachment; filename="resultats-'.$contest->reference.'-tour-2.pdf"');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('%%EOF', $response->getContent());
        $this->get(route('admin.contests.pdf', [$contest, 3]))->assertNotFound();
        $this->actingAs(User::factory()->create(['role' => 'student']));
        $this->get(route('admin.contests.pdf', [$contest, 2]))->assertForbidden();
        $this->post(route('admin.contests.entries.store', [$contest, 2]), $this->entry())->assertForbidden();
        $this->delete(route('admin.contests.entries.clear', [$contest, 2]))->assertForbidden();
    }

    public function test_pdf_supports_multiple_pages_and_an_empty_round(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        for ($rank = 1; $rank <= 100; $rank++) {
            $contest->entries()->create($this->entry(['last_name' => 'ÉTUDIANT '.$rank, 'first_names' => 'Chloé André', 'rank' => $rank]) + ['round' => 1]);
        }
        $response = $this->get(route('admin.contests.pdf', [$contest, 1]))->assertOk();
        $this->assertGreaterThan(1, preg_match_all('/\/Type\s*\/Page\b/', $response->getContent()));
        $empty = $this->get(route('admin.contests.pdf', [$contest, 2]))->assertOk();
        $this->assertSame(1, preg_match_all('/\/Type\s*\/Page\b/', $empty->getContent()));
    }

    public function test_registration_number_is_preserved_edited_and_searchable(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->post(route('admin.contests.entries.store', [$contest, 1]), $this->entry(['registration_number' => '000123']))->assertSessionHasNoErrors();
        $entry = $contest->entries()->firstOrFail();
        $this->assertSame('000123', $entry->registration_number);
        $this->get(route('admin.contests.entries.edit', [$contest, 1, $entry]))->assertOk()->assertSee('000123');
        $this->put(route('admin.contests.entries.update', [$contest, 1, $entry]), $this->entry(['registration_number' => 'INSG-0042']))->assertSessionHasNoErrors();
        $this->assertSame('INSG-0042', $entry->fresh()->registration_number);
        $this->post(route('admin.contests.publish', $contest), ['round' => 1])->assertSessionHasNoErrors();
        $this->post(route('contests.results.search'), ['registration_number' => 'INSG-0042', 'verification_code' => '2027'])->assertOk()->assertSee('Alice')->assertSee('INSG-0042');
        $this->get(route('admin.contests.export', [$contest, 'round' => 1]))->assertStreamedContent("\xEF\xBB\xBFMatricule;\"Date de naissance\";Nom;Prénom;Filière;Moyenne;Décision;Rang\nINSG-0042;;MBA;Alice;Gestion;12.75;Admis(e);7\n");
    }

    public function test_excel_import_preserves_registration_numbers_as_text(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $file = new \Illuminate\Http\UploadedFile(base_path('tests/Fixtures/contest-entries-matricules.xlsx'), 'etudiants.xlsx', null, null, true);
        $this->post(route('admin.contests.entries.import', [$contest, 2]), ['excel_file' => $file])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('contest_entries', ['contest_id' => $contest->id, 'round' => 2, 'registration_number' => '0000123']);
        $this->assertDatabaseHas('contest_entries', ['contest_id' => $contest->id, 'round' => 2, 'registration_number' => 'INSG-0042']);
        $this->assertSame('2004-02-29', $contest->entries()->where('registration_number', '0000123')->firstOrFail()->birth_date->format('Y-m-d'));
        $this->assertSame('2003-06-15', $contest->entries()->where('registration_number', 'INSG-0042')->firstOrFail()->birth_date->format('Y-m-d'));
    }

    public function test_public_results_require_a_search_and_hide_grades(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 1, [$this->entry(['registration_number' => 'TEST-001', 'birth_date' => '2004-02-29', 'decision' => 'Ajourné(e)'])], true)->assertSessionHasNoErrors();
        $this->get(route('contests.results', ['contest_id' => $contest->id, 'round' => 1]))->assertOk()->assertDontSee('TEST-001')->assertDontSee('Alice');
        $response = $this->post(route('contests.results.search'), ['registration_number' => 'TEST-001', 'verification_code' => '2027']);
        $response->assertOk()->assertSee('MBA Alice')->assertSee('29/02/2004')->assertSee('Gestion')->assertSee('AJOURNÉ(E)')->assertSee('is-adjourned')->assertDontSee('Moyenne')->assertDontSee('Mention')->assertDontSee('12,75')->assertDontSee('<th>Rang</th>', false);
    }

    public function test_birth_date_and_decision_are_validated(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->post(route('admin.contests.entries.store', [$contest, 1]), $this->entry(['birth_date' => '2040-01-01', 'decision' => 'Autre']))->assertSessionHasErrors(['birth_date', 'decision']);
        $this->post(route('admin.contests.entries.store', [$contest, 1]), $this->entry(['birth_date' => '2004-02-29']))->assertSessionHasNoErrors();
        $entry = $contest->entries()->firstOrFail();
        $this->get(route('admin.contests.entries.edit', [$contest, 1, $entry]))->assertOk()->assertSee('2004-02-29');
    }

    public function test_confidential_code_must_match_exam_year_and_legacy_search_is_disabled(): void
    {
        $admin = $this->admin(); $contest = $this->contest($admin); $this->actingAs($admin);
        $this->save($contest, 1, [$this->entry()], true);
        $this->post(route('contests.results.search'), ['registration_number' => 'TEST-2027', 'verification_code' => '2026'])->assertOk()->assertSee('Aucun résultat disponible')->assertDontSee('MBA Alice');
        $this->post(route('contests.results.search'), ['registration_number' => 'MBA', 'verification_code' => '2027'])->assertOk()->assertDontSee('MBA Alice');
        $this->post(route('contests.results.search'), ['registration_number' => 'TEST-2027'])->assertSessionHasErrors('verification_code');
        $this->get(route('contests.results', ['q' => 'MBA', 'contest_id' => $contest->id, 'round' => 1]))->assertOk()->assertDontSee('MBA Alice')->assertDontSee('name="q"', false)->assertDontSee('<select', false);
        $this->post(route('contests.results.search'), ['registration_number' => 'TEST-2027', 'verification_code' => '2027'])->assertOk()->assertSee('MBA Alice')->assertSee('candidate-result')->assertDontSee('<table', false)->assertDontSee('Moyenne');
    }

    public function test_example_credentials_return_the_demo_result(): void
    {
        $this->seed(\Database\Seeders\ContestResultExampleSeeder::class);
        $this->post(route('contests.results.search'), ['registration_number' => 'TEST-INSG-2026-001', 'verification_code' => '2026'])->assertOk()->assertSee('EXEMPLE Camille')->assertSee('12/04/2005')->assertSee('ADMIS(E)')->assertDontSee('<table', false);
    }
}

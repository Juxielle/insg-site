<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\ContestApplication;
use App\Models\User;
use App\Services\ContestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContestModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); $this->seed(); Storage::fake('local');
    }

    public function test_admin_can_create_and_open_a_contest(): void
    {
        $this->actingAs($this->admin())->post(route('admin.contests.store'), $this->contestPayload())
            ->assertRedirect();
        $contest = Contest::where('title', 'Concours test 2027')->firstOrFail();
        $this->assertMatchesRegularExpression('/^CONC-2027-\d{3}$/', $contest->reference);
        $this->put(route('admin.contests.transition', $contest), ['status' => 'registration_open'])->assertRedirect();
        $this->assertSame('registration_open', $contest->fresh()->status);
    }

    public function test_public_application_is_pending_then_validation_assigns_identifiers(): void
    {
        $contest = Contest::where('status', 'registration_open')->firstOrFail();
        $payload = $this->candidatePayload() + ['documents' => [UploadedFile::fake()->create('bac.pdf', 100, 'application/pdf')], 'consent' => '1'];
        $this->post(route('contests.store', $contest), $payload)->assertRedirect();
        $application = ContestApplication::with('candidate')->latest('id')->firstOrFail();
        $this->assertSame('pending', $application->status);
        $this->assertNull($application->candidate->registration_number);

        $this->actingAs($this->admin())->put(route('admin.contests.review', $application), ['status' => 'validated'])->assertRedirect();
        $application->refresh()->load('candidate');
        $this->assertMatchesRegularExpression('/^INSG-2026-\d{4}$/', $application->candidate->registration_number);
        $this->assertNotNull($application->candidate_number);

        $duplicate = $payload; $duplicate['documents'] = [UploadedFile::fake()->create('bac-2.pdf', 100, 'application/pdf')];
        $this->post(route('contests.store', $contest), $duplicate)->assertSessionHasErrors('email');
    }

    public function test_closed_contest_rejects_public_registration(): void
    {
        $contest = Contest::where('status', 'registration_open')->firstOrFail();
        $contest->update(['status' => 'registration_closed']);
        $this->post(route('contests.store', $contest), $this->candidatePayload() + ['documents' => [UploadedFile::fake()->create('bac.pdf', 10, 'application/pdf')], 'consent' => '1'])
            ->assertSessionHasErrors('contest');
        $this->assertDatabaseMissing('contest_applications', ['contest_id' => $contest->id]);
    }

    public function test_mentions_decisions_ranking_publication_and_confidential_search(): void
    {
        $contest = Contest::where('status', 'registration_open')->firstOrFail();
        $service = app(ContestService::class); $admin = $this->admin();
        $first = $service->submitApplication($contest, $this->candidatePayload(), [], 'admin', $admin);
        $secondData = $this->candidatePayload(); $secondData['email'] = 'second@example.com'; $secondData['last_name'] = 'Second';
        $second = $service->submitApplication($contest, $secondData, [], 'admin', $admin);
        $contest->update(['status' => 'completed']);

        $service->saveResults($contest, [$first->id => 15.75, $second->id => 15.75], $admin);
        $this->assertDatabaseHas('contest_results', ['contest_application_id' => $first->id, 'mention' => 'Bien', 'decision' => 'admitted', 'rank' => 1]);
        $this->assertDatabaseHas('contest_results', ['contest_application_id' => $second->id, 'rank' => 1]);
        $service->validateResults($contest->fresh(), $admin); $service->publish($contest->fresh(), $admin);
        $this->get(route('home'))->assertOk()->assertSee('Résultats des concours disponibles');

        $first->refresh()->load('candidate');
        $this->post(route('contests.results.search'), ['contest_id' => $contest->id, 'registration_number' => $first->candidate->registration_number, 'verification_code' => $first->verification_code])
            ->assertOk()->assertSee('15,75')->assertSee('ADMIS')->assertDontSee($first->candidate->email);

        $service->saveResults($contest->fresh(), [$first->id => 8], $admin);
        $this->assertNull($contest->fresh()->published_at);
        $this->assertDatabaseHas('contest_results', ['contest_application_id' => $first->id, 'mention' => null, 'decision' => 'not_admitted']);
    }

    public function test_incomplete_results_cannot_be_published_and_average_is_validated(): void
    {
        $contest = Contest::where('status', 'registration_open')->firstOrFail(); $service = app(ContestService::class); $admin = $this->admin();
        $application = $service->submitApplication($contest, $this->candidatePayload(), [], 'admin', $admin);
        $contest->update(['status' => 'completed']);
        $this->expectException(ValidationException::class);
        $service->validateResults($contest->fresh(), $admin);
    }

    public function test_non_admin_cannot_access_contest_management(): void
    {
        $this->get(route('admin.contests.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.contests.index'))->assertForbidden();
    }

    private function admin(): User { return User::where('role', 'admin')->firstOrFail(); }
    private function candidatePayload(): array { return ['last_name'=>'Mba','first_names'=>'Paul','gender'=>'M','birth_date'=>'2002-05-10','birth_place'=>'Libreville','nationality'=>'Gabonaise','phone'=>'060000000','email'=>'paul.concours@example.com','address'=>'Akanda','city'=>'Libreville','province'=>'Estuaire','study_level'=>'Terminale','previous_school'=>'Lycée national','diploma'=>'Baccalauréat','graduation_year'=>2026,'field'=>'B','specialty'=>'Économie']; }
    private function contestPayload(): array { return ['title'=>'Concours test 2027','description'=>'Description complète du concours.','academic_year'=>'2027-2028','session'=>'Juin 2027','type'=>'Entrée','registration_starts_at'=>'2027-01-01T08:00','registration_ends_at'=>'2027-05-31T18:00','exam_date'=>'2027-06-15','exam_time'=>'08:00','location'=>'INSG','available_places'=>100,'additional_information'=>'Instructions']; }
}

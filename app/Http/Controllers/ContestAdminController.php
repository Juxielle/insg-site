<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\ContestApplication;
use App\Models\ContestSubject;
use App\Models\ContestTrack;
use App\Services\ContestService;
use App\Services\ContestExcelImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class ContestAdminController extends Controller
{
    public function __construct(private ContestService $service, private ContestExcelImportService $excelImporter) {}

    public function index(Request $request): View
    {
        $this->admin($request);
        $query = Contest::withCount('applications')->latest();
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('title', 'like', '%'.$request->q.'%')->orWhere('reference', 'like', '%'.$request->q.'%'));
        if ($request->filled('status')) $query->where('status', $request->status);
        return view('admin.contests.index', $this->base(['contests' => $query->paginate(15)->withQueryString()]));
    }

    public function create(Request $request): View { $this->admin($request); return view('admin.contests.form', $this->base(['contest' => null])); }

    public function store(Request $request): RedirectResponse
    {
        $this->admin($request);
        $request->validate(['results_file' => ['nullable', 'file', 'mimes:xlsx', 'max:10240']]);
        $contest = $this->service->createContest($this->contestData($request), $request->user());
        $message = 'Concours créé avec ses filières et matières par défaut.';
        if ($request->hasFile('results_file')) {
            $report = $this->excelImporter->import($contest, $request->file('results_file'), $request->user());
            $message .= " {$report['imported']} candidat(s) importé(s).";
            if ($report['errors']) $request->session()->flash('import_errors', $report['errors']);
        }
        return redirect()->route('admin.contests.show', $contest)->with('backoffice_success', $message);
    }

    public function edit(Request $request, Contest $contest): View
    {
        $this->admin($request);
        abort_if(in_array($contest->status, ['results_published', 'archived'], true), 422, 'Ce concours ne peut plus être modifié librement.');
        return view('admin.contests.form', $this->base(compact('contest')));
    }

    public function update(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        abort_if(in_array($contest->status, ['results_published', 'archived'], true), 422);
        $contest->update($this->contestData($request));
        return redirect()->route('admin.contests.show', $contest)->with('backoffice_success', 'Concours mis à jour.');
    }

    public function show(Request $request, Contest $contest): View
    {
        $this->admin($request);
        $contest->loadCount([
            'applications',
            'applications as pending_count' => fn ($q) => $q->where('status', 'pending'),
            'applications as validated_count' => fn ($q) => $q->where('status', 'validated'),
            'applications as rejected_count' => fn ($q) => $q->where('status', 'rejected'),
            'applications as results_count' => fn ($q) => $q->whereHas('result'),
            'applications as admitted_count' => fn ($q) => $q->whereHas('result', fn ($r) => $r->where('decision', 'admitted')),
        ]);
        $contest->load('tracks.subjects');
        return view('admin.contests.show', $this->base(compact('contest')));
    }

    public function storeTrack(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['name' => ['required', 'string', 'max:150', Rule::unique('contest_tracks')->where('contest_id', $contest->id)]]);
        $track = $contest->tracks()->create($data + ['sort_order' => $contest->tracks()->max('sort_order') + 1]);
        foreach (['Français', 'Mathématiques', 'Anglais', 'Oral'] as $index => $name) $track->subjects()->create(['name' => $name, 'max_score' => 20, 'sort_order' => $index + 1]);
        return back()->with('backoffice_success', 'Filière ajoutée avec les matières par défaut.');
    }

    public function destroyTrack(Request $request, Contest $contest, ContestTrack $track): RedirectResponse
    {
        $this->admin($request); abort_unless($track->contest_id === $contest->id, 404);
        if ($track->applications()->exists()) return back()->withErrors(['track' => 'Cette filière contient déjà des candidats et ne peut pas être supprimée.']);
        $track->delete(); return back()->with('backoffice_success', 'Filière supprimée.');
    }

    public function storeSubject(Request $request, Contest $contest, ContestTrack $track): RedirectResponse
    {
        $this->admin($request); abort_unless($track->contest_id === $contest->id, 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:120', Rule::unique('contest_subjects')->where('contest_track_id', $track->id)], 'max_score' => ['required', 'numeric', 'gt:0', 'max:1000']]);
        $track->subjects()->create($data + ['sort_order' => $track->subjects()->max('sort_order') + 1]);
        return back()->with('backoffice_success', 'Matière ajoutée.');
    }

    public function updateSubject(Request $request, Contest $contest, ContestSubject $subject): RedirectResponse
    {
        $this->admin($request); abort_unless($subject->track?->contest_id === $contest->id, 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'max_score' => ['required', 'numeric', 'gt:0', 'max:1000']]);
        $subject->update($data); return back()->with('backoffice_success', 'Matière mise à jour.');
    }

    public function transition(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $request->validate(['status' => ['required', 'string']]);
        $this->service->transition($contest, $request->status, $request->user());
        return back()->with('backoffice_success', 'Statut du concours mis à jour.');
    }

    public function applications(Request $request, Contest $contest): View
    {
        $this->admin($request);
        $query = $contest->applications()->with(['candidate', 'track', 'result'])->latest('submitted_at');
        if ($request->filled('q')) $query->whereHas('candidate', fn ($q) => $q->where('last_name', 'like', '%'.$request->q.'%')->orWhere('first_names', 'like', '%'.$request->q.'%')->orWhere('registration_number', 'like', '%'.$request->q.'%'));
        if ($request->filled('status')) $query->where('status', $request->status);
        return view('admin.contests.applications', $this->base(['contest' => $contest, 'applications' => $query->paginate(20)->withQueryString()]));
    }

    public function application(Request $request, ContestApplication $application): View
    {
        $this->admin($request);
        return view('admin.contests.application', $this->base(['application' => $application->load(['contest', 'track', 'candidate', 'reviewer', 'result'])]));
    }

    public function createApplication(Request $request, Contest $contest): View
    {
        $this->admin($request);
        abort_unless($contest->isRegistrationOpen(), 422, 'Les inscriptions ne sont pas ouvertes.');
        $contest->load('tracks');
        return view('admin.contests.application-form', $this->base(compact('contest')));
    }

    public function storeApplication(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $candidate = $request->validate([
            'contest_track_id' => ['required', Rule::exists('contest_tracks', 'id')->where('contest_id', $contest->id)],
            'last_name' => ['required','string','max:100'], 'first_names' => ['required','string','max:150'], 'gender' => ['nullable',Rule::in(['F','M'])],
            'birth_date' => ['required','date','before:today'], 'birth_place' => ['nullable','string','max:150'], 'nationality' => ['required','string','max:100'],
            'phone' => ['required','string','max:40'], 'email' => ['required','email','max:255'], 'address' => ['nullable','string','max:255'], 'city' => ['nullable','string','max:100'], 'province' => ['nullable','string','max:100'],
            'study_level' => ['required','string','max:100'], 'previous_school' => ['nullable','string','max:255'], 'diploma' => ['required','string','max:150'], 'graduation_year' => ['nullable','integer','between:1990,'.(date('Y')+1)], 'field' => ['nullable','string','max:150'], 'specialty' => ['nullable','string','max:150'],
        ]);
        $trackId = (int) Arr::pull($candidate, 'contest_track_id');
        $application = $this->service->submitApplication($contest, $candidate, [], 'admin', $request->user(), $trackId);
        return redirect()->route('admin.contests.application', $application)->with('backoffice_success', 'Candidat inscrit et validé avec succès.');
    }

    public function review(Request $request, ContestApplication $application): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['status' => ['required', Rule::in(['validated', 'rejected', 'correction_required'])], 'observations' => ['nullable', 'string', 'max:3000']]);
        $this->service->review($application, $data['status'], $data['observations'] ?? null, $request->user());
        return back()->with('backoffice_success', 'Candidature mise à jour.');
    }

    public function downloadDocument(Request $request, ContestApplication $application, int $document): StreamedResponse
    {
        $this->admin($request);
        $file = collect($application->documents ?? [])->get($document);
        abort_unless(is_array($file) && Storage::disk('local')->exists($file['path'] ?? ''), 404);
        return Storage::disk('local')->download($file['path'], $file['name'] ?? basename($file['path']));
    }

    public function results(Request $request, Contest $contest): View
    {
        $this->admin($request);
        $query = $contest->applications()->where('status', 'validated')->with(['candidate', 'track.subjects', 'scores', 'result']);
        if ($request->filled('q')) $query->whereHas('candidate', fn ($q) => $q->where('last_name', 'like', '%'.$request->q.'%')->orWhere('registration_number', 'like', '%'.$request->q.'%'));
        return view('admin.contests.results', $this->base(['contest' => $contest, 'applications' => $query->paginate(50)->withQueryString()]));
    }

    public function saveResults(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['scores' => ['required', 'array'], 'scores.*' => ['array']]);
        $this->service->saveScores($contest, $data['scores'], $request->user());
        return back()->with('backoffice_success', 'Notes enregistrées, moyennes et classement recalculés.');
    }

    public function validateResults(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request); $this->service->validateResults($contest, $request->user());
        return back()->with('backoffice_success', 'Résultats validés. Ils peuvent maintenant être publiés.');
    }

    public function publish(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request); $this->service->publish($contest, $request->user());
        return back()->with('backoffice_success', 'Les résultats ont été publiés avec succès.');
    }

    public function unpublish(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request); $this->service->unpublish($contest, $request->user());
        return back()->with('backoffice_success', 'Les résultats ont été dépubliés.');
    }

    public function export(Contest $contest): StreamedResponse
    {
        abort_unless(request()->user()?->role === 'admin', 403);
        $rows = $contest->applications()->where('status', 'validated')->with(['candidate', 'track', 'result'])->get();
        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w'); fputcsv($out, ['Rang', 'Filière', 'Matricule', 'Numéro candidat', 'Nom', 'Prénoms', 'Moyenne', 'Mention', 'Décision'], ';');
            foreach ($rows as $row) fputcsv($out, [$row->result?->rank, $row->track?->name, $row->candidate->registration_number, $row->candidate_number, $row->candidate->last_name, $row->candidate->first_names, $row->result?->average, $row->result?->mention, $row->result?->decision], ';');
            fclose($out);
        }, 'resultats-'.$contest->reference.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function convocation(Request $request, ContestApplication $application): View
    {
        $this->admin($request); abort_unless($application->status === 'validated', 404);
        return view('admin.contests.convocation', ['application' => $application->load(['contest', 'candidate'])]);
    }

    public function officialList(Request $request, Contest $contest): View
    {
        $this->admin($request);
        $applications = $contest->applications()->where('status', 'validated')
            ->whereHas('result', fn ($query) => $query->where('decision', 'admitted'))
            ->with(['candidate', 'result'])->get()->sortBy('result.rank');
        return view('admin.contests.official-list', compact('contest', 'applications'));
    }

    public function importPreview(Request $request, Contest $contest): View
    {
        $this->admin($request);
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = array_map(fn ($value) => Str::lower(trim($value)), fgetcsv($handle, 0, ',') ?: []);
        if ($header !== ['matricule', 'moyenne']) throw \Illuminate\Validation\ValidationException::withMessages(['csv_file' => 'Le fichier doit contenir les colonnes matricule,moyenne.']);
        $rows = []; $errors = []; $seen = [];
        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            $matricule = trim($line[0] ?? ''); $average = trim($line[1] ?? '');
            $application = $contest->applications()->where('status', 'validated')->whereHas('candidate', fn ($q) => $q->where('registration_number', $matricule))->first();
            if (isset($seen[$matricule])) $errors[] = "Matricule en double : {$matricule}";
            elseif (! $application) $errors[] = "Candidat introuvable : {$matricule}";
            elseif (! is_numeric($average) || $average < 0 || $average > 20) $errors[] = "Moyenne invalide pour {$matricule}";
            else { $rows[$application->id] = (float) $average; $seen[$matricule] = true; }
        }
        fclose($handle);
        $token = Str::random(32); $request->session()->put('contest_import_'.$token, $rows);
        return view('admin.contests.import', $this->base(compact('contest', 'rows', 'errors', 'token')));
    }

    public function importConfirm(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request); $data = $request->validate(['token' => ['required', 'string', 'size:32']]);
        $rows = $request->session()->pull('contest_import_'.$data['token']);
        abort_unless(is_array($rows) && $rows !== [], 422, 'Import expiré ou vide.');
        $this->service->saveResults($contest, $rows, $request->user());
        return redirect()->route('admin.contests.results', $contest)->with('backoffice_success', count($rows).' résultat(s) importé(s).');
    }

    private function contestData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'], 'description' => ['required', 'string'],
            'academic_year' => ['required', 'regex:/^20\d{2}-20\d{2}$/'], 'session' => ['required', 'string', 'max:100'], 'type' => ['required', 'string', 'max:100'],
            'registration_starts_at' => ['required', 'date'], 'registration_ends_at' => ['required', 'date', 'after:registration_starts_at'],
            'exam_date' => ['required', 'date', 'after_or_equal:registration_ends_at'], 'exam_time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'], 'available_places' => ['required', 'integer', 'min:1'], 'additional_information' => ['nullable', 'string'],
        ]);
    }

    private function admin(Request $request): void { abort_unless($request->user()?->role === 'admin', 403); }
    private function base(array $data = []): array { return $data + ['resources' => (new ContentAdminController)->resources()]; }
}

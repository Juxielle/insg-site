<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContestAdminController extends Controller
{
    public function __construct(private ContestService $service) {}

    public function index(Request $request): View
    {
        $this->admin($request);
        $query = Contest::withCount('entries')->latest();
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('title', 'like', '%'.$request->q.'%')->orWhere('reference', 'like', '%'.$request->q.'%'));
        if ($request->filled('status')) $query->where('status', $request->status);
        return view('admin.contests.index', $this->base(['contests' => $query->paginate(15)->withQueryString()]));
    }

    public function create(Request $request): View { $this->admin($request); return view('admin.contests.form', $this->base(['contest' => null])); }

    public function store(Request $request): RedirectResponse
    {
        $this->admin($request);
        $contest = $this->service->createContest($this->contestData($request), $request->user());
        $message = 'Concours créé. Renseignez les étudiants du premier tour.';
        return redirect()->route('admin.contests.show', $contest)->with('backoffice_success', $message);
    }

    public function edit(Request $request, Contest $contest): View
    {
        $this->admin($request);
        abort_if(in_array($contest->status, ['archived'], true), 422, 'Ce concours ne peut plus être modifié librement.');
        return view('admin.contests.form', $this->base(compact('contest')));
    }

    public function update(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        abort_if(in_array($contest->status, ['archived'], true), 422);
        $contest->update($this->contestData($request));
        return redirect()->route('admin.contests.show', $contest)->with('backoffice_success', 'Concours mis à jour.');
    }

    public function show(Request $request, Contest $contest): View
    {
        $this->admin($request);
        return view('admin.contests.show', $this->base(['contest' => $contest->load('rounds')->loadCount('entries')]));
    }

    private function roundNumber(Request $request): int
    {
        return (int) ($request->validate(['round' => ['sometimes', 'required', 'integer', Rule::in([1, 2])]])['round'] ?? 1);
    }

    public function results(Request $request, Contest $contest): View
    {
        $this->admin($request);
        $round = $this->roundNumber($request);
        $entries = $contest->entries()->where('round', $round)->orderBy('rank')->orderBy('id')->get();
        $published = $contest->rounds()->where('number', $round)->whereNotNull('published_at')->exists();
        return view('admin.contests.results', $this->base(compact('contest', 'round', 'entries', 'published')));
    }

    public function saveResults(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $round = $this->roundNumber($request);
        if ($request->has('entries_json')) {
            $request->validate(['entries_json' => ['required', 'json', 'max:2000000']]);
            $request->merge(['entries' => json_decode($request->input('entries_json'), true)]);
        }
        $data = $request->validate([
            'entries' => ['required', 'array', 'min:1', 'max:500'],
            'entries.*' => ['required', 'array:birth_date,registration_number,last_name,first_names,field,average,decision,rank'],
            'entries.*.birth_date' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'entries.*.registration_number' => ['nullable', 'string', 'max:50'],
            'publish' => ['sometimes', 'boolean'],
            'entries.*.last_name' => ['required', 'string', 'max:100'],
            'entries.*.first_names' => ['required', 'string', 'max:150'],
            'entries.*.field' => ['required', 'string', 'max:150'],
            'entries.*.average' => ['required', 'numeric', 'between:0,20'],
            'entries.*.decision' => ['required', Rule::in(['Admis(e)', 'Ajourné(e)'])],
            'entries.*.rank' => ['required', 'integer', 'min:1', 'max:2147483647'],
        ]);
        DB::transaction(function () use ($contest, $round, $data, $request): void {
            $contest = Contest::lockForUpdate()->findOrFail($contest->id);
            if ($contest->status === 'archived' || $contest->rounds()->where('number', $round)->whereNotNull('published_at')->exists()) {
                throw ValidationException::withMessages(['entries' => 'Dépubliez ce tour avant de modifier ses résultats.']);
            }
            $contest->entries()->where('round', $round)->delete();
            foreach ($data['entries'] as $entry) $contest->entries()->create($entry + ['round' => $round]);
            if ($request->boolean('publish')) $this->publishRound($contest, $round);
        });
        return redirect()->route('admin.contests.results', [$contest, 'round' => $round])->with('backoffice_success', $request->boolean('publish') ? 'Tour publié avec succès.' : 'étudiants enregistrés.');
    }

    private function publishRound(Contest $contest, int $round): void
    {
        if ($contest->status === 'archived') throw ValidationException::withMessages(['round' => 'Ce concours est archivé.']);
        if ($round === 2 && ! $contest->rounds()->where('number', 1)->whereNotNull('published_at')->exists()) {
            throw ValidationException::withMessages(['round' => 'Publiez le premier tour avant le deuxième tour.']);
        }
        $entries = $contest->entries()->where('round', $round)->get();
        if ($entries->isEmpty() || $entries->contains(fn ($entry) => blank($entry->field) || $entry->average === null || ! in_array($entry->decision, ['Admis(e)', 'Ajourné(e)'], true) || ! $entry->rank)) {
            throw ValidationException::withMessages(['entries' => 'Renseignez tous les champs des étudiants avant publication.']);
        }
        $contest->rounds()->updateOrCreate(['number' => $round], ['published_at' => now()]);
        $contest->update(['status' => 'results_published', 'published_at' => now(), 'published_by' => request()->user()->id]);
    }

    public function publish(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $round = $this->roundNumber($request);
        DB::transaction(fn () => $this->publishRound(Contest::lockForUpdate()->findOrFail($contest->id), $round));
        return back()->with('backoffice_success', 'Tour publié avec succès.');
    }

    public function unpublish(Request $request, Contest $contest): RedirectResponse
    {
        $this->admin($request);
        $round = $this->roundNumber($request);
        DB::transaction(function () use ($contest, $round): void {
            $contest = Contest::lockForUpdate()->findOrFail($contest->id);
            if ($round === 1 && $contest->rounds()->where('number', 2)->whereNotNull('published_at')->exists()) {
                throw ValidationException::withMessages(['round' => 'Dépubliez le deuxième tour avant le premier tour.']);
            }
            $contest->rounds()->where('number', $round)->update(['published_at' => null]);
            if (! $contest->rounds()->whereNotNull('published_at')->exists()) $contest->update(['status' => 'results_preparation', 'published_at' => null, 'published_by' => null]);
        });
        return back()->with('backoffice_success', 'Tour dépublié. Vous pouvez modifier les étudiants.');
    }

    public function export(Request $request, Contest $contest): StreamedResponse
    {
        $this->admin($request);
        $round = $this->roundNumber($request);
        $rows = $contest->entries()->where('round', $round)->orderBy('rank')->get();
        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Matricule', 'Date de naissance', 'Nom', 'Prénom', 'Filière', 'Moyenne', 'Décision', 'Rang'], ';', '"', '');
            foreach ($rows as $row) {
                $cells = [$row->registration_number, $row->birth_date?->format('d/m/Y'), $row->last_name, $row->first_names, $row->field, $row->average, $row->decision, $row->rank];
                fputcsv($out, array_map(fn ($v) => preg_match('/^[=+@\-\t\r]/', (string) $v) ? "'".$v : $v, $cells), ';', '"', '');
            }
            fclose($out);
        }, 'resultats-'.$contest->reference.'-tour-'.$round.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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

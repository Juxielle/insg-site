<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\ContestApplication;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContestPublicController extends Controller
{
    public function __construct(private ContestService $service) {}

    public function index(): View { return view('contests.index', ['contests' => Contest::where('status', 'registration_open')->orderBy('exam_date')->get()]); }

    public function create(Contest $contest): View { abort_unless($contest->isRegistrationOpen(), 404); return view('contests.apply', compact('contest')); }

    public function store(Request $request, Contest $contest): RedirectResponse
    {
        $data = $request->validate([
            'last_name' => ['required', 'string', 'max:100'], 'first_names' => ['required', 'string', 'max:150'], 'gender' => ['required', Rule::in(['F', 'M'])],
            'birth_date' => ['required', 'date', 'before:today'], 'birth_place' => ['required', 'string', 'max:150'], 'nationality' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:40'], 'email' => ['required', 'email', 'max:255'], 'address' => ['nullable', 'string', 'max:255'], 'city' => ['required', 'string', 'max:100'], 'province' => ['nullable', 'string', 'max:100'],
            'study_level' => ['required', 'string', 'max:100'], 'previous_school' => ['nullable', 'string', 'max:255'], 'diploma' => ['required', 'string', 'max:150'], 'graduation_year' => ['nullable', 'integer', 'between:1990,'.(date('Y') + 1)], 'field' => ['nullable', 'string', 'max:150'], 'specialty' => ['nullable', 'string', 'max:150'],
            'documents' => ['required', 'array', 'max:10'], 'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], 'consent' => ['accepted'],
        ]);
        $documents = collect($request->file('documents'))->map(fn ($file) => ['name' => $file->getClientOriginalName(), 'path' => $file->store('contests/'.$contest->id)])->all();
        $application = $this->service->submitApplication($contest, collect($data)->except(['documents', 'consent'])->all(), $documents);
        return redirect()->route('contests.confirmation', $application->verification_code);
    }

    public function confirmation(string $code): View
    {
        $application = ContestApplication::with('contest')->where('verification_code', $code)->firstOrFail();
        return view('contests.confirmation', ['application' => $application, 'resultsCode' => date('Y')]);
    }

    public function results(): View { return view('contests.results', ['contests' => Contest::where('status', 'results_published')->latest('published_at')->get(), 'result' => null]); }

    public function search(Request $request): View
    {
        $data = $request->validate([
            'contest_id' => ['required', 'integer'],
            'registration_number' => ['required', 'string', 'max:50'],
            'verification_code' => ['required', 'digits:4', Rule::in([date('Y')])],
        ]);
        $contest = Contest::where('status', 'results_published')->findOrFail($data['contest_id']);
        $application = $contest->applications()->with(['candidate', 'result'])->whereHas('candidate', fn ($q) => $q->where('registration_number', $data['registration_number']))->first();
        return view('contests.results', ['contests' => Contest::where('status', 'results_published')->latest('published_at')->get(), 'result' => $application]);
    }
}

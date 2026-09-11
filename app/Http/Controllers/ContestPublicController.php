<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\ContestEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContestPublicController extends Controller
{
    public function index(): View { return view('contests.index', ['contests' => Contest::where('status', 'results_published')->latest('published_at')->get()]); }

    public function results(): View
    {
        return view('contests.results', ['results' => collect(), 'searched' => false, 'registrationNumber' => '']);
    }

    public function search(Request $request): View
    {
        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:50'],
            'verification_code' => ['required', 'digits:4'],
        ]);
        // The code is the year of the examination, not the current year.
        $results = ContestEntry::select('id', 'contest_id', 'round', 'registration_number', 'last_name', 'first_names', 'birth_date', 'field', 'decision')
            ->with('contest:id,title,reference,session,academic_year,exam_date')
            ->where('registration_number', $data['registration_number'])
            ->whereIn('decision', ['Admis(e)', 'Ajourné(e)'])
            ->whereHas('contest', fn ($query) => $query->where('status', 'results_published')->whereYear('exam_date', $data['verification_code']))
            ->whereExists(fn ($query) => $query->selectRaw('1')->from('contest_rounds')
                ->whereColumn('contest_rounds.contest_id', 'contest_entries.contest_id')
                ->whereColumn('contest_rounds.number', 'contest_entries.round')->whereNotNull('contest_rounds.published_at'))
            ->orderByDesc('round')->orderByDesc('id')->get()
            ->unique('contest_id')->values();

        return view('contests.results', ['results' => $results, 'searched' => true, 'registrationNumber' => $data['registration_number']]);
    }
}

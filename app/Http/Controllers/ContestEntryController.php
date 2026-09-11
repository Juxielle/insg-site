<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Services\ContestEntryImportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContestEntryController extends Controller
{
    private function admin(Request $request): void { abort_unless($request->user()?->role === 'admin', 403); }

    private function editable(Contest $contest, int $round): void
    {
        abort_unless(in_array($round, [1, 2], true), 404);
        if ($contest->status === 'archived') throw ValidationException::withMessages(['round' => 'Ce concours est archivé.']);
        if ($contest->rounds()->where('number', $round)->whereNotNull('published_at')->exists()) {
            throw ValidationException::withMessages(['round' => 'Dépubliez ce tour avant de modifier sa liste.']);
        }
    }

    private function mutate(Contest $contest, int $round, callable $callback): void
    {
        DB::transaction(function () use ($contest, $round, $callback): void {
            $contest = Contest::lockForUpdate()->findOrFail($contest->id);
            $this->editable($contest, $round);
            $callback($contest);
        });
    }

    private function done(Contest $contest, int $round, string $message): RedirectResponse
    {
        return redirect()->route('admin.contests.results', [$contest, 'round' => $round])->with('backoffice_success', $message);
    }

    public function create(Request $request, Contest $contest, int $round): View
    {
        $this->admin($request); $this->editable($contest, $round);
        return view('admin.contests.entry-form', ['contest' => $contest, 'round' => $round, 'entry' => null, 'resources' => (new ContentAdminController)->resources()]);
    }

    public function store(Request $request, Contest $contest, int $round): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(ContestEntryImportService::rules());
        $this->mutate($contest, $round, fn ($contest) => $contest->entries()->create($data + ['round' => $round]));
        return $this->done($contest, $round, 'Étudiant ajouté au tour '.$round.'.');
    }

    public function edit(Request $request, Contest $contest, int $round, int $entry): View
    {
        $this->admin($request); $this->editable($contest, $round);
        $entry = $contest->entries()->where('round', $round)->findOrFail($entry);
        return view('admin.contests.entry-form', ['contest' => $contest, 'round' => $round, 'entry' => $entry, 'resources' => (new ContentAdminController)->resources()]);
    }

    public function update(Request $request, Contest $contest, int $round, int $entry): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(ContestEntryImportService::rules());
        $this->mutate($contest, $round, fn ($contest) => $contest->entries()->where('round', $round)->findOrFail($entry)->update($data));
        return $this->done($contest, $round, 'Étudiant modifié.');
    }

    public function destroy(Request $request, Contest $contest, int $round, int $entry): RedirectResponse
    {
        $this->admin($request);
        $this->mutate($contest, $round, fn ($contest) => $contest->entries()->where('round', $round)->findOrFail($entry)->delete());
        return $this->done($contest, $round, 'Étudiant supprimé de ce tour.');
    }

    public function clear(Request $request, Contest $contest, int $round): RedirectResponse
    {
        $this->admin($request);
        $this->mutate($contest, $round, fn ($contest) => $contest->entries()->where('round', $round)->delete());
        return $this->done($contest, $round, 'La liste du tour '.$round.' a été vidée.');
    }

    public function import(Request $request, Contest $contest, int $round, ContestEntryImportService $importer): RedirectResponse
    {
        $this->admin($request); $this->editable($contest, $round);
        $request->validate(['excel_file' => ['required', 'file', 'mimes:xlsx', 'max:10240']]);
        $entries = $importer->read($request->file('excel_file'));
        $this->mutate($contest, $round, function ($contest) use ($entries, $round): void {
            foreach ($entries as $entry) $contest->entries()->create($entry + ['round' => $round]);
        });
        return $this->done($contest, $round, count($entries).' étudiant(s) ajouté(s) au tour '.$round.'.');
    }

    public function pdf(Request $request, Contest $contest, int $round): Response
    {
        $this->admin($request);
        abort_unless(in_array($round, [1, 2], true), 404);
        $entries = $contest->entries()->where('round', $round)->orderBy('rank')->orderBy('last_name')->get();
        $published = $contest->rounds()->where('number', $round)->value('published_at');
        $options = new Options(['isRemoteEnabled' => false, 'isPhpEnabled' => false, 'defaultFont' => 'DejaVu Sans', 'tempDir' => storage_path('app')]);
        $pdf = new Dompdf($options);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHtml(view('admin.contests.pdf', compact('contest', 'round', 'entries', 'published'))->render(), 'UTF-8');
        $pdf->render();
        $pdf->getCanvas()->page_text(745, 570, '{PAGE_NUM} / {PAGE_COUNT}', $pdf->getFontMetrics()->getFont('DejaVu Sans'), 8);
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="resultats-'.$contest->reference.'-tour-'.$round.'.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

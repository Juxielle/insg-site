<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function contact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:40'],
            'sujet' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Submission::create([
            'type' => 'contact',
            'name' => $validated['nom'],
            'email' => $validated['email'],
            'phone' => $validated['telephone'] ?? null,
            'data' => ['sujet' => $validated['sujet'], 'message' => $validated['message']],
        ]);

        return back()->with('success', 'contact');
    }

    public function admission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:150'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'telephone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
            'filiere' => ['required', 'string', 'max:100'],
            'niveau' => ['required', 'string', 'max:50'],
            'documents' => ['required', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'consentement' => ['accepted'],
        ]);

        $documents = $this->storeFiles($request->file('documents', []), 'admissions');

        Submission::create([
            'type' => 'admission',
            'name' => "{$validated['prenom']} {$validated['nom']}",
            'email' => $validated['email'],
            'phone' => $validated['telephone'],
            'data' => collect($validated)->except(['documents', 'consentement', 'email', 'telephone'])->all(),
            'documents' => $documents,
        ]);

        return back()->with('success', 'admission');
    }

    public function master(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:150'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'nationalite' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
            'residence' => ['required', 'string', 'max:255'],
            'specialite' => ['required', 'string', 'max:100'],
            'niveau' => ['required', 'string', 'max:50'],
            'dernier_diplome' => ['required', 'string', 'max:255'],
            'etablissement' => ['required', 'string', 'max:255'],
            'annee_obtention' => ['required', 'integer', 'between:1990,2030'],
            'mention' => ['nullable', 'string', 'max:50'],
            'moyenne' => ['nullable', 'numeric', 'between:0,20'],
            'projet_professionnel' => ['required', 'string', 'min:100', 'max:5000'],
            'diplome' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'releves_notes' => ['required', 'array', 'max:10'],
            'releves_notes.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'lettre_motivation' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'piece_identite' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'autres_documents' => ['nullable', 'array', 'max:10'],
            'autres_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'declaration' => ['accepted'],
            'consentement' => ['accepted'],
        ]);

        $files = collect($request->allFiles())->flatten()->filter(fn ($file) => $file instanceof UploadedFile);
        $documents = $this->storeFiles($files->all(), 'masters');

        $submission = Submission::create([
            'type' => 'master',
            'name' => "{$validated['prenom']} {$validated['nom']}",
            'email' => $validated['email'],
            'phone' => $validated['telephone'],
            'data' => collect($validated)->except([
                'diplome', 'releves_notes', 'cv', 'lettre_motivation', 'piece_identite',
                'autres_documents', 'declaration', 'consentement', 'email', 'telephone',
            ])->all(),
            'documents' => $documents,
        ]);

        $submission->update([
            'tracking_number' => sprintf('MASTER-%s-%06d', date('Y'), $submission->id),
        ]);

        return back()->with([
            'success' => 'master',
            'tracking_number' => $submission->tracking_number,
        ]);
    }

    public function masterTracking(): View
    {
        return view('master.tracking', ['submission' => null]);
    }

    public function trackMaster(Request $request): View
    {
        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $submission = Submission::where('type', 'master')
            ->where('tracking_number', strtoupper(trim($validated['tracking_number'])))
            ->where('email', $validated['email'])
            ->first();

        return view('master.tracking', compact('submission'))
            ->with('searched', true);
    }

    private function storeFiles(array $files, string $directory): array
    {
        return collect($files)
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->map(fn (UploadedFile $file) => [
                'name' => $file->getClientOriginalName(),
                'path' => $file->store($directory),
            ])->values()->all();
    }
}

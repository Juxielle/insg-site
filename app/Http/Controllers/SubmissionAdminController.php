<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(['contact', 'admission', 'master'])],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $query = Submission::query()->latest();
        if (filled($validated['type'] ?? null)) $query->where('type', $validated['type']);
        if (filled($validated['status'] ?? null)) $query->where('status', $validated['status']);

        return view('admin.submissions.index', [
            'resources' => (new ContentAdminController)->resources(),
            'submissions' => $query->paginate(20)->withQueryString(),
            'counts' => Submission::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Request $request, Submission $submission): View
    {
        $this->authorizeAdmin($request);

        return view('admin.submissions.show', [
            'resources' => (new ContentAdminController)->resources(),
            'submission' => $submission->load('reviewer'),
        ]);
    }

    public function updateStatus(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'admin_note' => ['nullable', 'string', 'max:3000'],
        ]);

        $submission->update($validated + [
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('backoffice_success', 'Le statut de la demande a été mis à jour.');
    }

    public function download(Request $request, Submission $submission, int $document): StreamedResponse
    {
        $this->authorizeAdmin($request);
        $file = collect($submission->documents ?? [])->get($document);
        abort_unless(is_array($file) && filled($file['path'] ?? null) && Storage::disk('local')->exists($file['path']), 404);

        return Storage::disk('local')->download($file['path'], $file['name'] ?? basename($file['path']));
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}

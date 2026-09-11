@extends('admin.content.layout')
@section('title', 'Résultats du concours')
@section('content')
@php($editable = !$published && $contest->status !== 'archived')
<a href="{{ route('admin.contests.show', $contest) }}" class="btn btn-outline-secondary mb-3">Retour au concours</a>
<h1 class="h2">{{ $contest->title }} — {{ $round === 1 ? 'Premier tour' : 'Deuxième tour' }}</h1>
<nav class="d-flex flex-wrap gap-2 my-4">@foreach([1,2] as $number)<a class="btn {{ $round === $number ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.contests.results', [$contest, 'round' => $number]) }}">Tour {{ $number }}</a>@endforeach</nav>
<div class="cms-card p-4 mb-4">
<div class="d-flex flex-wrap gap-2">
@if($editable)
<a class="btn btn-primary" href="{{ route('admin.contests.entries.create', [$contest, $round]) }}"><i class="bi bi-person-plus me-2"></i>Ajouter un étudiant manuellement</a>
<button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#excel-import" aria-expanded="{{ $errors->has('excel_file') ? 'true' : 'false' }}" aria-controls="excel-import"><i class="bi bi-file-earmark-excel me-2"></i>Importer un fichier Excel</button>
@else
<span class="text-muted align-self-center">{{ $published ? 'Dépubliez ce tour pour ajouter, modifier ou supprimer des étudiants.' : 'Ce concours est archivé.' }}</span>
@endif
<a class="btn btn-outline-primary" href="{{ route('admin.contests.pdf', [$contest, $round]) }}"><i class="bi bi-file-earmark-pdf me-2"></i>Télécharger les résultats en PDF</a>
<a class="btn btn-outline-secondary" href="{{ route('admin.contests.export', [$contest, 'round' => $round]) }}">Exporter CSV</a>
@if($editable && $entries->isNotEmpty())
<form method="POST" action="{{ route('admin.contests.entries.clear', [$contest, $round]) }}" onsubmit="return confirm('Vider toute la liste du tour {{ $round }} ? Cette suppression est définitive.')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-2"></i>Vider la liste du tour {{ $round }}</button></form>
@endif
</div>
@if($editable)
<div class="collapse {{ $errors->has('excel_file') ? 'show' : '' }}" id="excel-import">
<form method="POST" enctype="multipart/form-data" class="border-top mt-4 pt-4" action="{{ route('admin.contests.entries.import', [$contest, $round]) }}">@csrf
<h2 class="h5">Ajouter des étudiants au tour {{ $round }} depuis Excel</h2>
<p>La première feuille doit contenir les colonnes <strong>nom, prénom, filière, moyenne, décision, rang</strong> et peut inclure les colonnes <strong>matricule</strong> et <strong>date de naissance</strong> (JJ/MM/AAAA ou AAAA-MM-JJ) sur sa première ligne. Décisions acceptées : Admis(e) ou Ajourné(e). Les étudiants seront ajoutés à la liste existante. Si une ligne est invalide, aucun étudiant ne sera importé.</p>
<label class="form-label" for="excel-file">Fichier Excel (.xlsx, 10 Mo maximum, 5 000 lignes maximum)</label>
<div class="d-flex flex-wrap gap-2"><input class="form-control" style="max-width:500px" id="excel-file" type="file" name="excel_file" accept=".xlsx" required><button class="btn btn-primary">Importer dans le tour {{ $round }}</button></div>
</form>
</div>
@endif
</div>
@if($published)
<div class="alert alert-success">Les résultats de ce tour sont publiés.</div>
@elseif($contest->status !== 'archived')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3"><p class="mb-0">Liste en préparation — {{ $entries->count() }} étudiant(s).</p>
@if($entries->isNotEmpty())<form method="POST" action="{{ route('admin.contests.publish', $contest) }}">@csrf<input type="hidden" name="round" value="{{ $round }}"><button class="btn btn-success">Publier le tour {{ $round }}</button></form>@endif</div>
@endif
<div class="cms-card p-3"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Rang</th><th>Matricule</th><th>Date de naissance</th><th>Nom</th><th>Prénom</th><th>Filière</th><th>Moyenne / 20</th><th>Décision</th>@if($editable)<th>Actions</th>@endif</tr></thead><tbody>
@forelse($entries as $entry)
<tr><td>{{ $entry->rank }}</td><td>{{ $entry->registration_number ?: '—' }}</td><td>{{ $entry->birth_date?->format('d/m/Y') ?: '—' }}</td><td>{{ $entry->last_name }}</td><td>{{ $entry->first_names }}</td><td>{{ $entry->field }}</td><td>{{ $entry->average === null ? '—' : number_format((float) $entry->average, 2, ',', ' ') }}</td><td>{{ $entry->decision }}</td>
@if($editable)<td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.contests.entries.edit', [$contest, $round, $entry]) }}">Modifier</a><form method="POST" action="{{ route('admin.contests.entries.destroy', [$contest, $round, $entry]) }}" onsubmit="return confirm('Supprimer cet étudiant du tour {{ $round }} ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Supprimer</button></form></div></td>@endif</tr>
@empty<tr><td colspan="{{ $editable ? 9 : 8 }}" class="text-center text-muted py-4">Aucun étudiant dans ce tour. Ajoutez un étudiant manuellement ou importez un fichier Excel.</td></tr>@endforelse
</tbody></table></div></div>
@if($published && $contest->status !== 'archived')
<form method="POST" action="{{ route('admin.contests.unpublish', $contest) }}" class="mt-3">@csrf<input type="hidden" name="round" value="{{ $round }}"><button class="btn btn-outline-danger">Dépublier ce tour pour le modifier</button></form>
@endif
@endsection
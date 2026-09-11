@extends('admin.content.layout')
@section('title', $entry ? 'Modifier un étudiant' : 'Ajouter un étudiant')
@section('content')
<a class="btn btn-outline-secondary mb-3" href="{{ route('admin.contests.results', [$contest, 'round' => $round]) }}">Retour à la liste</a>
<p class="text-muted">{{ $contest->title }} — Tour {{ $round }}</p>
<h1 class="h2 mb-4">{{ $entry ? 'Modifier un étudiant' : 'Ajouter un étudiant manuellement' }}</h1>
<form method="POST" class="cms-card cms-form p-4" action="{{ $entry ? route('admin.contests.entries.update', [$contest, $round, $entry]) : route('admin.contests.entries.store', [$contest, $round]) }}">
@csrf @if($entry) @method('PUT') @endif
<div class="row g-4">
<div class="col-md-6"><label class="form-label" for="birth_date">Date de naissance</label><input class="form-control" type="date" id="birth_date" name="birth_date" max="{{ now()->subDay()->format('Y-m-d') }}" value="{{ old('birth_date', $entry?->birth_date?->format('Y-m-d')) }}"></div>
<div class="col-md-6"><label class="form-label" for="decision">Décision</label><select class="form-select" id="decision" name="decision" required><option value="">Sélectionner</option>@foreach(['Admis(e)', 'Ajourné(e)'] as $decision)<option value="{{ $decision }}" @selected(old('decision', $entry?->decision) === $decision)>{{ $decision }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label" for="registration_number">Matricule <span class="text-muted">(facultatif)</span></label><input class="form-control" id="registration_number" name="registration_number" maxlength="50" value="{{ old('registration_number', $entry?->registration_number) }}"></div>
@foreach(['last_name' => ['Nom',100], 'first_names' => ['Prénom',150], 'field' => ['Filière',150]] as $key => [$label, $max])
<div class="col-md-6"><label class="form-label" for="{{ $key }}">{{ $label }}</label><input class="form-control" id="{{ $key }}" name="{{ $key }}" maxlength="{{ $max }}" value="{{ old($key, $entry?->$key) }}" required></div>
@endforeach
<div class="col-md-6"><label class="form-label" for="average">Moyenne / 20</label><input class="form-control" type="number" id="average" name="average" min="0" max="20" step="0.01" value="{{ old('average', $entry?->average) }}" required></div>
<div class="col-md-6"><label class="form-label" for="rank">Rang</label><input class="form-control" type="number" id="rank" name="rank" min="1" max="2147483647" value="{{ old('rank', $entry?->rank) }}" required></div>
</div>
<div class="d-flex gap-2 mt-4"><button class="btn btn-primary">{{ $entry ? 'Enregistrer les modifications' : 'Ajouter au tour '.$round }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.contests.results', [$contest, 'round' => $round]) }}">Annuler</a></div>
</form>
@endsection

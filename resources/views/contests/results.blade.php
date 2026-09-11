@extends('contests.layout')
@section('title', 'Mon résultat de concours')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow text-white">Espace candidat</span><h1>Mon résultat de concours</h1><p>Retrouvez votre résultat à partir de votre matricule et de votre code confidentiel.</p></div></section>
<section class="section"><div class="container result-space">
<form method="POST" action="{{ route('contests.results.search') }}" class="form-panel result-search">
@csrf
<div class="result-search-heading"><span class="result-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span><div><h2 class="h4 mb-1">Consulter mon résultat</h2><p class="text-muted mb-0">Les deux informations ci-dessous sont nécessaires.</p></div></div>
@if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="row g-3">
<div class="col-md-6"><label class="form-label" for="registration_number">Matricule du concours</label><input class="form-control" id="registration_number" name="registration_number" value="{{ old('registration_number', $registrationNumber) }}" maxlength="50" autocomplete="off" required></div>
<div class="col-md-6"><label class="form-label" for="verification_code">Code confidentiel</label><input class="form-control" id="verification_code" name="verification_code" type="password" inputmode="numeric" pattern="[0-9]{4}" minlength="4" maxlength="4" autocomplete="off" aria-describedby="code-help" required><div class="form-text" id="code-help">Saisissez l’année du concours (4 chiffres).</div></div>
</div><button class="btn btn-insg-primary mt-4" type="submit"><i class="bi bi-search me-2" aria-hidden="true"></i>Afficher mon résultat</button>
</form>
@if($searched)
<div class="mt-4" aria-live="polite">
@forelse($results as $result)
<article class="candidate-result mb-4" aria-labelledby="result-title-{{ $result->id }}">
<header class="candidate-result-header"><div><span class="result-kicker">Résultat officiel · Tour {{ $result->round }}</span><h2 id="result-title-{{ $result->id }}" class="h4 mt-2 mb-1">{{ $result->contest->title }}</h2><p class="mb-0">{{ $result->contest->session }} · {{ $result->contest->academic_year }}</p></div><i class="bi bi-patch-check result-seal" aria-hidden="true"></i></header>
<div class="candidate-result-body">
<p class="result-kicker text-muted mb-1">Candidat</p><h3 class="candidate-name">{{ $result->last_name }} {{ $result->first_names }}</h3>
<dl class="candidate-details">
<div><dt><i class="bi bi-person-badge me-2" aria-hidden="true"></i>Matricule</dt><dd>{{ $result->registration_number }}</dd></div>
<div><dt><i class="bi bi-calendar3 me-2" aria-hidden="true"></i>Date de naissance</dt><dd>{{ $result->birth_date?->format('d/m/Y') ?: 'Non renseignée' }}</dd></div>
<div class="candidate-field"><dt><i class="bi bi-mortarboard me-2" aria-hidden="true"></i>Filière</dt><dd>{{ $result->field }}</dd></div>
</dl>
<div class="candidate-decision {{ $result->decision === 'Admis(e)' ? 'is-admitted' : 'is-adjourned' }}">
<i class="bi {{ $result->decision === 'Admis(e)' ? 'bi-check-circle' : 'bi-x-circle' }}" aria-hidden="true"></i><div><span>Décision du jury</span><strong>{{ $result->decision === 'Admis(e)' ? 'ADMIS(E)' : 'AJOURNÉ(E)' }}</strong></div>
</div>
</div><footer class="candidate-result-footer"><i class="bi bi-info-circle me-2" aria-hidden="true"></i>Dernier tour publié pour ce candidat.</footer>
</article>
@empty
<div class="form-panel text-center py-5" role="status"><i class="bi bi-search fs-2 text-muted" aria-hidden="true"></i><h2 class="h5 mt-3">Aucun résultat disponible</h2><p class="text-muted mb-0">Vérifiez votre matricule et votre code confidentiel. Seuls les résultats publiés sont consultables.</p></div>
@endforelse
</div>
@endif
</div></section>
@endsection

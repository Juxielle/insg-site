@extends('admin.content.layout')
@section('title', $contest->title)
@section('content')
@php
  $statuses = ['draft'=>'Brouillon','registration_open'=>'Inscriptions ouvertes','registration_closed'=>'Inscriptions clôturées','completed'=>'Concours terminé','results_preparation'=>'Résultats en préparation','results_published'=>'Résultats publiés','archived'=>'Archivé'];
  $next = [
    'draft'=>['registration_open'=>'Ouvrir les inscriptions','archived'=>'Archiver'],
    'registration_open'=>['registration_closed'=>'Clôturer les inscriptions','draft'=>'Repasser en brouillon'],
    'registration_closed'=>['completed'=>'Marquer comme terminé','registration_open'=>'Rouvrir les inscriptions'],
    'completed'=>['results_preparation'=>'Préparer les résultats'],
    'results_preparation'=>['completed'=>'Revenir au concours terminé'],
    'results_published'=>['archived'=>'Archiver'],
    'archived'=>[],
  ];
  $statistics = [
    ['Candidats', $contest->applications_count, 'bi-people'],
    ['En attente', $contest->pending_count, 'bi-hourglass-split'],
    ['Validés', $contest->validated_count, 'bi-check-circle'],
    ['Refusés', $contest->rejected_count, 'bi-x-circle'],
    ['Résultats saisis', $contest->results_count, 'bi-clipboard-data'],
    ['Admis', $contest->admitted_count, 'bi-award'],
  ];
@endphp

<div class="d-flex flex-wrap align-items-start gap-3 mb-4">
  <div class="flex-grow-1"><span class="cms-topbar-kicker">{{ $contest->reference }}</span><h1 class="h2 mb-1">{{ $contest->title }}</h1><p class="text-muted mb-0">{{ $contest->session }} · {{ $contest->academic_year }}</p></div>
  <span class="badge cms-status {{ $contest->status === 'results_published' ? 'approved' : 'pending' }}">{{ $statuses[$contest->status] }}</span>
  @if(!in_array($contest->status, ['results_published', 'archived']))
    <a class="btn btn-outline-primary" href="{{ route('admin.contests.edit', $contest) }}"><i class="bi bi-pencil me-2"></i>Modifier</a>
  @endif
</div>

<div class="row g-3 mb-4">
  @foreach($statistics as [$label, $value, $icon])
    <div class="col-6 col-lg-2"><div class="cms-card cms-mini-stat"><i class="bi {{ $icon }}"></i><strong>{{ $value }}</strong><span>{{ $label }}</span></div></div>
  @endforeach
</div>

<div class="row g-4">
  <div class="col-xl-8"><div class="cms-card p-4">
    <h2 class="h5 cms-section-title"><i class="bi bi-info-circle"></i>Informations</h2>
    <dl class="cms-data-grid">
      <div><dt>Date</dt><dd>{{ $contest->exam_date->format('d/m/Y') }} à {{ substr($contest->exam_time, 0, 5) }}</dd></div>
      <div><dt>Lieu</dt><dd>{{ $contest->location }}</dd></div>
      <div><dt>Inscriptions</dt><dd>Du {{ $contest->registration_starts_at->format('d/m/Y H:i') }} au {{ $contest->registration_ends_at->format('d/m/Y H:i') }}</dd></div>
      <div><dt>Places</dt><dd>{{ $contest->available_places }}</dd></div>
    </dl>
    <div class="d-flex flex-wrap gap-2 mt-4">
      <a class="btn btn-primary" href="{{ route('admin.contests.applications', $contest) }}"><i class="bi bi-people me-2"></i>Gérer les candidatures</a>
      <a class="btn btn-outline-primary" href="{{ route('admin.contests.results', $contest) }}"><i class="bi bi-clipboard-data me-2"></i>Gérer les résultats</a>
      <a class="btn btn-outline-secondary" href="{{ route('admin.contests.export', $contest) }}"><i class="bi bi-download me-2"></i>Exporter CSV</a>
    </div>
  </div></div>
  <div class="col-xl-4"><div class="cms-card p-4">
    <h2 class="h5 cms-section-title"><i class="bi bi-diagram-3"></i>Cycle du concours</h2>
    @foreach($next[$contest->status] ?? [] as $status => $label)
      <form method="POST" action="{{ route('admin.contests.transition', $contest) }}" class="mb-2" onsubmit="return confirm('Confirmer ce changement de statut ?')">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="{{ $status }}">
        <button class="btn btn-outline-primary w-100">{{ $label }}</button>
      </form>
    @endforeach

    @if($contest->status === 'results_preparation')
      <form method="POST" action="{{ route('admin.contests.results.validate', $contest) }}" class="mb-2">@csrf<button class="btn btn-outline-primary w-100">Valider les résultats</button></form>
      <form method="POST" action="{{ route('admin.contests.publish', $contest) }}" onsubmit="return confirm('Publier officiellement ces résultats ?')">@csrf<button class="btn btn-primary w-100">Publier les résultats</button></form>
    @elseif($contest->status === 'results_published')
      <form method="POST" action="{{ route('admin.contests.unpublish', $contest) }}" onsubmit="return confirm('Dépublier les résultats ?')">@csrf<button class="btn btn-outline-danger w-100">Dépublier les résultats</button></form>
    @endif
  </div></div>
</div>

@if(!in_array($contest->status, ['results_published', 'archived']))
<section class="cms-card p-4 mt-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h2 class="h5 cms-section-title mb-1"><i class="bi bi-diagram-3"></i>Filières et matières</h2><p class="text-muted mb-0">Configurez les épreuves et leur barème maximal pour chaque filière.</p></div><form method="POST" action="{{ route('admin.contests.tracks.store', $contest) }}" class="d-flex gap-2">@csrf<input class="form-control" name="name" placeholder="Nouvelle filière" required><button class="btn btn-primary text-nowrap"><i class="bi bi-plus-lg me-1"></i>Ajouter</button></form></div>
  @if(session('import_errors'))<div class="alert alert-warning"><strong>Import Excel partiel :</strong><ul class="mb-0">@foreach(session('import_errors') as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  <div class="accordion" id="tracksAccordion">
    @foreach($contest->tracks as $track)
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#track-{{ $track->id }}"><strong>{{ $track->name }}</strong><span class="badge text-bg-light ms-2">{{ $track->subjects->count() }} matières</span></button></h3><div id="track-{{ $track->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#tracksAccordion"><div class="accordion-body">
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Matière</th><th style="width:180px">Score maximal</th><th style="width:100px"></th></tr></thead><tbody>@foreach($track->subjects as $subject)<tr><form method="POST" action="{{ route('admin.contests.subjects.update', [$contest, $subject]) }}">@csrf @method('PUT')<td><input class="form-control" name="name" value="{{ $subject->name }}" required></td><td><div class="input-group"><input class="form-control" type="number" min="0.01" max="1000" step=".01" name="max_score" value="{{ $subject->max_score }}" required><span class="input-group-text">points</span></div></td><td><button class="btn btn-outline-primary btn-sm">Enregistrer</button></td></form></tr>@endforeach</tbody></table></div>
        <form method="POST" action="{{ route('admin.contests.subjects.store', [$contest, $track]) }}" class="row g-2 align-items-end">@csrf<div class="col-md-6"><label class="form-label">Nouvelle matière</label><input class="form-control" name="name" required></div><div class="col-md-3"><label class="form-label">Score maximal</label><input class="form-control" type="number" min="0.01" max="1000" step=".01" name="max_score" value="20" required></div><div class="col-md-3"><button class="btn btn-outline-primary w-100">Ajouter la matière</button></div></form>
        @if(!$track->applications()->exists())<form method="POST" action="{{ route('admin.contests.tracks.destroy', [$contest, $track]) }}" class="mt-3 text-end" onsubmit="return confirm('Supprimer cette filière et ses matières ?')">@csrf @method('DELETE')<button class="btn btn-link text-danger btn-sm">Supprimer la filière</button></form>@endif
      </div></div></div>
    @endforeach
  </div>
</section>
@endif
@endsection

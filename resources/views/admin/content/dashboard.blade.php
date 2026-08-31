@extends('admin.content.layout')
@section('title', 'Gestion du site')
@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Gestion des contenus</h1><p class="text-muted mb-0">Pilotez les informations publiées sur le site institutionnel.</p></div>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-eye me-2"></i>Prévisualiser le site</a>
  </div>
  <div class="cms-submission-overview mb-4">
    <div>
      <span class="cms-icon"><i class="bi bi-inbox"></i></span>
      <div><span class="cms-topbar-kicker">Suivi des formulaires</span><h2 class="h5 mb-1">Demandes et inscriptions</h2><p class="text-muted small mb-0">Contact, concours et candidatures en Master.</p></div>
    </div>
    <div class="cms-submission-metrics">
      <span><strong>{{ $submissionCounts->get('contact', 0) }}</strong>Messages</span>
      <span><strong>{{ $submissionCounts->get('admission', 0) }}</strong>Concours</span>
      <span><strong>{{ $submissionCounts->get('master', 0) }}</strong>Masters</span>
      <span class="pending"><strong>{{ $pendingSubmissions }}</strong>À traiter</span>
    </div>
    <a href="{{ route('admin.content.submissions.index') }}" class="btn btn-primary">Ouvrir les demandes<i class="bi bi-arrow-right ms-2"></i></a>
  </div>
  <div class="row g-4">
    @foreach ($resources as $key => $config)
      <div class="col-md-6 col-xl-4">
        <div class="cms-card h-100"><a class="cms-stat" href="{{ route('admin.content.index', $key) }}">
          <div class="d-flex justify-content-between align-items-start"><span class="cms-icon"><i class="bi {{ $config['icon'] }}"></i></span><span class="badge text-bg-light">{{ $counts[$key] }}</span></div>
          <h2 class="h5 mt-4 mb-1">{{ $config['label'] }}</h2><p class="text-muted small mb-0">Ajouter, modifier, publier ou supprimer.</p>
        </a></div>
      </div>
    @endforeach
  </div>
@endsection

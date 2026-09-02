@extends('contests.layout')
@section('title', 'Suivi de candidature Master')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow text-white">Admission sur dossier</span><h1>Suivre ma candidature en Master</h1><p>Consultez l’avancement de l’examen de votre dossier à l’aide du numéro reçu après son dépôt.</p></div></section>
<section class="section"><div class="container"><div class="row justify-content-center"><div class="col-xl-8">
  <form method="POST" action="{{ route('master.track') }}" class="form-panel mb-4">@csrf
    <h2 class="h4 mb-3">Rechercher mon dossier</h2>
    @if($errors->any())<div class="alert alert-danger">Veuillez vérifier les informations saisies.</div>@endif
    <div class="row g-3"><div class="col-md-6"><label class="form-label" for="tracking_number">Numéro de suivi</label><input class="form-control text-uppercase" id="tracking_number" name="tracking_number" value="{{ old('tracking_number') }}" placeholder="MASTER-{{ date('Y') }}-000001" required></div><div class="col-md-6"><label class="form-label" for="email">Adresse email</label><input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required></div></div>
    <button class="btn btn-insg-primary mt-3" type="submit"><i class="bi bi-search me-2"></i>Consulter l’état du dossier</button>
  </form>
  @if(isset($searched))
    @if($submission)
      @php($statuses = ['pending' => ['En cours d’examen', 'warning', 'Votre dossier a été reçu et doit être examiné par la commission.'], 'approved' => ['Admis', 'success', 'Votre candidature a été retenue. L’administration vous communiquera les prochaines étapes.'], 'rejected' => ['Non admis', 'danger', 'Votre candidature n’a pas été retenue à l’issue de l’examen du dossier.']])
      @php($state = $statuses[$submission->status] ?? [$submission->status, 'secondary', ''])
      <article class="form-panel"><span class="badge text-bg-{{ $state[1] }} mb-3">{{ $state[0] }}</span><h2 class="h4">Dossier {{ $submission->tracking_number }}</h2><p class="mb-2"><strong>Candidat :</strong> {{ $submission->name }}</p><p class="mb-3"><strong>Formation demandée :</strong> {{ $submission->data['niveau'] ?? '' }} — {{ $submission->data['specialite'] ?? '' }}</p><div class="alert alert-{{ $state[1] }} mb-0">{{ $state[2] }}</div></article>
    @else
      <div class="alert alert-warning">Aucun dossier ne correspond à ce numéro de suivi et à cette adresse email.</div>
    @endif
  @endif
</div></div></div></section>
@endsection

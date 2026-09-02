@extends('admin.content.layout')
@section('title', 'Demande de '.$submission->name)
@section('content')
  @php
    $typeLabels = ['contact' => 'Message de contact', 'admission' => 'Inscription au concours', 'master' => 'Inscription en Master'];
    $statusLabels = ['pending' => 'En attente', 'approved' => 'Validée', 'rejected' => 'Refusée'];
  @endphp
  <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
    <a href="{{ route('admin.content.submissions.index') }}" class="btn btn-outline-secondary" aria-label="Retour"><i class="bi bi-arrow-left"></i></a>
    <div class="flex-grow-1"><span class="cms-topbar-kicker">{{ $typeLabels[$submission->type] ?? $submission->type }}</span><h1 class="h2 mb-0">{{ $submission->name }}</h1></div>
    <span class="badge cms-status {{ $submission->status }}">{{ $statusLabels[$submission->status] ?? $submission->status }}</span>
  </div>
  <div class="row g-4">
    <div class="col-xl-8">
      <section class="cms-card p-4 mb-4"><h2 class="h5 cms-section-title"><i class="bi bi-person"></i>Coordonnées</h2><div class="row g-3">@if($submission->tracking_number)<div class="col-md-6"><span class="cms-data-label">Numéro de suivi</span><strong>{{ $submission->tracking_number }}</strong></div>@endif<div class="col-md-6"><span class="cms-data-label">Email</span><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></div><div class="col-md-6"><span class="cms-data-label">Téléphone</span>{{ $submission->phone ?: 'Non renseigné' }}</div><div class="col-md-6"><span class="cms-data-label">Reçue le</span>{{ $submission->created_at->format('d/m/Y à H:i') }}</div></div></section>
      <section class="cms-card p-4 mb-4"><h2 class="h5 cms-section-title"><i class="bi bi-card-text"></i>Informations transmises</h2><dl class="cms-data-grid mb-0">@foreach($submission->data as $key => $value)<div><dt>{{ ucfirst(str_replace('_', ' ', $key)) }}</dt><dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd></div>@endforeach</dl></section>
      @if($submission->documents)<section class="cms-card p-4"><h2 class="h5 cms-section-title"><i class="bi bi-paperclip"></i>Pièces jointes</h2><div class="cms-documents">@foreach($submission->documents as $index => $document)<a href="{{ route('admin.content.submissions.download', [$submission, $index]) }}" class="cms-document"><i class="bi bi-file-earmark-arrow-down"></i><span><strong>{{ $document['name'] ?? 'Document '.($index + 1) }}</strong><small>Télécharger le fichier</small></span></a>@endforeach</div></section>@endif
    </div>
    <div class="col-xl-4"><form method="POST" action="{{ route('admin.content.submissions.status', $submission) }}" class="cms-card p-4 cms-review-panel">@csrf @method('PUT')<h2 class="h5 cms-section-title"><i class="bi bi-check2-square"></i>Décision administrative</h2><label class="form-label" for="status">Statut de la demande</label><select class="form-select" name="status" id="status">@foreach($statusLabels as $value => $label)<option value="{{ $value }}" @selected($submission->status === $value)>{{ $label }}</option>@endforeach</select><label class="form-label mt-3" for="admin_note">Note interne</label><textarea class="form-control" name="admin_note" id="admin_note" rows="5" placeholder="Motif, observation ou information de suivi…">{{ old('admin_note', $submission->admin_note) }}</textarea><button class="btn btn-primary w-100 mt-3" type="submit"><i class="bi bi-check-lg me-2"></i>Enregistrer la décision</button>@if($submission->reviewed_at)<div class="cms-review-history"><i class="bi bi-clock-history"></i><span>Dernière décision le {{ $submission->reviewed_at->format('d/m/Y à H:i') }}@if($submission->reviewer)<br>par {{ $submission->reviewer->name }}@endif</span></div>@endif</form></div>
  </div>
@endsection

@extends('admin.content.layout')
@section('title', 'Demandes reçues')
@section('content')
  @php
    $typeLabels = ['contact' => 'Contact', 'admission' => 'Inscription au concours', 'master' => 'Inscription en Master'];
    $statusLabels = ['pending' => 'En attente', 'approved' => 'Validée', 'rejected' => 'Refusée'];
  @endphp
  <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><h1 class="h2 mb-1">Demandes reçues</h1><p class="text-muted mb-0">Consultez et traitez les formulaires envoyés depuis le site.</p></div>
    <div class="d-flex gap-2 flex-wrap"><span class="badge cms-count-badge pending">{{ $counts->get('pending', 0) }} en attente</span><span class="badge cms-count-badge approved">{{ $counts->get('approved', 0) }} validée(s)</span><span class="badge cms-count-badge rejected">{{ $counts->get('rejected', 0) }} refusée(s)</span></div>
  </div>
  <form method="GET" class="cms-card cms-filters p-3 mb-4">
    <div class="row g-3 align-items-end">
      <div class="col-md-5"><label class="form-label" for="type">Type de formulaire</label><select class="form-select" id="type" name="type"><option value="">Tous les formulaires</option>@foreach($typeLabels as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label" for="status">Statut</label><select class="form-select" id="status" name="status"><option value="">Tous les statuts</option>@foreach($statusLabels as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
      <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-funnel me-2"></i>Filtrer</button><a href="{{ route('admin.content.submissions.index') }}" class="btn btn-light" aria-label="Réinitialiser"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </div>
  </form>
  <div class="cms-card p-3 p-lg-4">
    <div class="table-responsive"><table class="table cms-table table-hover align-middle mb-0">
      <thead><tr><th>Demandeur</th><th>Formulaire</th><th>Réception</th><th>Statut</th><th class="text-end">Action</th></tr></thead>
      <tbody>@forelse($submissions as $submission)<tr>
        <td><strong>{{ $submission->name }}</strong><small class="d-block text-muted">{{ $submission->email }}</small></td>
        <td>{{ $typeLabels[$submission->type] ?? $submission->type }}</td>
        <td>{{ $submission->created_at->format('d/m/Y à H:i') }}</td>
        <td><span class="badge cms-status {{ $submission->status }}">{{ $statusLabels[$submission->status] ?? $submission->status }}</span></td>
        <td class="text-end"><a href="{{ route('admin.content.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Consulter</a></td>
      </tr>@empty<tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-inbox d-block fs-2 mb-2"></i>Aucune demande ne correspond aux filtres.</td></tr>@endforelse</tbody>
    </table></div>
    <div class="mt-4">{{ $submissions->links() }}</div>
  </div>
@endsection

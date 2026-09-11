@extends('admin.content.layout')
@section('title', $contest->title)
@section('content')
<div class="d-flex flex-wrap justify-content-between gap-3 mb-4"><div><p class="text-muted">{{ $contest->reference }} · {{ $contest->academic_year }}</p><h1 class="h2">{{ $contest->title }}</h1><p>{{ $contest->description }}</p></div><a class="btn btn-outline-primary align-self-start" href="{{ route('admin.contests.edit', $contest) }}">Modifier le concours</a></div>
<p class="mb-4">Renseignez les étudiants du premier tour et publiez leurs résultats, puis faites de même pour le deuxième tour.</p>
<div class="row g-4">
@foreach([1 => 'Premier tour', 2 => 'Deuxième tour'] as $number => $label)
@php($publication = $contest->rounds->firstWhere('number', $number)?->published_at)
<div class="col-md-6"><section class="cms-card p-4 h-100"><span class="badge {{ $publication ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $publication ? 'Publié' : 'En préparation' }}</span><h2 class="h4 mt-3">{{ $label }}</h2><p>{{ $contest->entries()->where('round', $number)->count() }} étudiant(s)</p>@if($publication)<p class="text-muted">Publié le {{ $publication->format('d/m/Y à H:i') }}</p>@endif<a class="btn btn-primary" href="{{ route('admin.contests.results', [$contest, 'round' => $number]) }}">Gérer les étudiants et résultats</a></section></div>
@endforeach
</div>
@endsection

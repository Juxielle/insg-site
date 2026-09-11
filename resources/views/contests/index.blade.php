@extends('contests.layout')
@section('title', 'Concours')
@section('content')
<section class="page-hero"><div class="container"><h1>Concours</h1><p>Consultez les résultats des concours de l’INSG.</p></div></section>
<section class="section"><div class="container"><div class="row g-4">@forelse($contests as $contest)<div class="col-lg-6"><article class="info-card h-100"><h2 class="h4">{{ $contest->title }}</h2><p>{{ $contest->description }}</p><p>{{ $contest->session }} · {{ $contest->academic_year }}</p><a class="btn btn-insg-primary" href="{{ route('contests.results', ['contest_id' => $contest->id]) }}">Consulter les résultats</a></article></div>@empty<p>Aucun résultat de concours publié pour le moment.</p>@endforelse</div></div></section>
@endsection

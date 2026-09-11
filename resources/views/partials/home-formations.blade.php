<section class="section{{ $background }}" data-cms-section="{{ $section->key }}">
  <div class="container">
    <div class="row justify-content-center text-center section-heading"><div class="col-lg-8">
      @if($section->eyebrow)<span class="eyebrow justify-content-center">{{ $section->eyebrow }}</span>@endif
      @if($section->title)<h2 class="section-title">{{ $section->title }}</h2>@endif
      <p class="section-lead mx-auto">Découvrez une sélection de filières proposées par l’INSG en BTS, Licence et Master.</p>
    </div></div>

    <div class="formation-groups">
      @foreach([
        ['home-bts', 'bi-journal-bookmark', 'BTS', '3 filières', ['Comptabilité et Gestion des Organisations (CGO)', 'Action Commerciale (AC)', 'Commerce International (CI)']],
        ['home-science-gestion', 'bi-mortarboard', 'Licence en science de gestion', '3 filières', ['Sciences de Gestion (LSG)', 'Finance Comptabilité (FC)', 'Marketing Commerce International (MCI)']],
        ['home-licence', 'bi-mortarboard', 'Licences professionnelles', '4 filières', ['Comptabilité Contrôle Audit (CCA)', 'Banque Finance (BF)', 'Informatique de Gestion (IG)', 'Gestion Économie Mines et Pétrole (GEMP)']],
        ['home-master', 'bi-award', 'Masters', '4 filières', ['Finance (FI)', 'Management des Affaires Internationales (MIA)', 'Management des Stratégies Commerciales (MSC)', 'Master Recherche en Sciences de Gestion (MRSG)']],
      ] as [$id, $icon, $title, $count, $courses])
        @include('partials.formation-list-group')
      @endforeach
    </div>

    <div class="text-center mt-5"><a href="{{ route('pages.formations') }}" class="btn btn-insg-navy">Voir toutes les formations <i class="bi bi-arrow-right ms-2"></i></a></div>
  </div>
</section>

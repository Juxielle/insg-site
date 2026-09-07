<section class="section{{ $background }} governance-chart-section" data-cms-section="{{ $section->key }}">
  <div class="container">
    <div class="row justify-content-center text-center section-heading"><div class="col-lg-9">
      @if($section->eyebrow)<span class="eyebrow justify-content-center">{{ $section->eyebrow }}</span>@endif
      @if($section->title)<h2 class="section-title">{{ $section->title }}</h2>@endif
      @if($section->body)<p class="section-lead mx-auto">{{ $section->body }}</p>@endif
    </div></div>
    <figure class="governance-chart mb-0">
      <a href="{{ asset('assets/images/organigramme-insg.jpeg') }}" target="_blank" rel="noopener" aria-label="Afficher l’organigramme de l’INSG en grand">
        <img src="{{ asset('assets/images/organigramme-insg.jpeg') }}" alt="Organigramme institutionnel de l’INSG présentant le Conseil d’Administration, la Direction générale, le pôle pédagogique et le pôle administratif et fonctionnel" loading="lazy">
        <span class="governance-chart-zoom"><i class="bi bi-arrows-fullscreen"></i> Agrandir</span>
      </a>
      <figcaption>Organisation administrative et pédagogique de l’Institut National des Sciences de Gestion.</figcaption>
    </figure>
  </div>
</section>

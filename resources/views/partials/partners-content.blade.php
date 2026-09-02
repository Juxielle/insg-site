<section class="section">
  <div class="container"><div class="row justify-content-center text-center section-heading"><div class="col-lg-9">
    <span class="eyebrow justify-content-center">Un réseau professionnel diversifié</span>
    <h2 class="section-title">Des partenaires engagés aux côtés de l’INSG</h2>
    <p class="section-lead mx-auto">Les étudiants de l’INSG sont accueillis dans un large réseau d’entreprises, d’institutions publiques et d’organismes privés. Cette diversité leur permet de découvrir différents environnements professionnels et de rapprocher leur formation des réalités du marché de l’emploi.</p>
  </div></div></div>
</section>

<section class="section bg-light-gray">
  <div class="container">
    <div class="row g-4 justify-content-center">
      @foreach([
        ['bi-bank', 'Banque et finance', 'Des établissements bancaires, financiers et de crédit qui accueillent et accompagnent nos étudiants.'],
        ['bi-clipboard-data', 'Audit et conseil', 'Des cabinets spécialisés qui favorisent la maîtrise des métiers de l’audit, du contrôle et du conseil.'],
        ['bi-truck', 'Industrie et logistique', 'Des entreprises issues du transport, de l’industrie, de l’énergie, du commerce et des services.'],
        ['bi-building', 'Secteur public', 'Des administrations et organismes parapublics associés à la professionnalisation des étudiants.'],
      ] as [$icon, $title, $text])
        <div class="col-md-6 col-xl-3"><article class="info-card h-100"><div class="icon-box"><i class="bi {{ $icon }}"></i></div><h3 class="h5">{{ $title }}</h3><p class="text-muted-insg mb-0">{{ $text }}</p></article></div>
      @endforeach
    </div>
  </div>
</section>

<section class="section" id="reseau-partenaires">
  <div class="container">
    <div class="row justify-content-center text-center section-heading"><div class="col-lg-8"><span class="eyebrow justify-content-center">Structures d’accueil</span><h2 class="section-title">Notre réseau de partenaires</h2><p class="section-lead mx-auto">Consultez les partenaires de l’Institut, regroupés par secteur d’activité.</p></div></div>
    <div class="formation-accordion accordion" id="partnersAccordion">
      @foreach($partners->groupBy('category') as $category => $categoryPartners)
        @php($panelId = 'partners-'.Str::slug($category))
        <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $panelId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $panelId }}"><span class="formation-accordion-icon"><i class="bi bi-buildings"></i></span><span class="flex-grow-1">{{ $category }}</span><span class="formation-count">{{ $categoryPartners->count() }} partenaires</span></button></h3><div id="{{ $panelId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#partnersAccordion"><div class="accordion-body"><div class="partner-name-grid">@foreach($categoryPartners as $partner)<div class="partner-name-card">@if($partner->logo_url)<img src="{{ $partner->logo_url }}" alt="Logo {{ $partner->name }}">@else<div class="partner-initials">{{ Str::of($partner->name)->replaceMatches('/[^\pL\pN ]/u', '')->explode(' ')->filter()->take(2)->map(fn($word) => Str::substr($word, 0, 1))->implode('') }}</div>@endif<span>{{ $partner->name }}</span></div>@endforeach</div></div></div></div>
      @endforeach
    </div>
  </div>
</section>

<section class="section bg-light-gray"><div class="container"><div class="row gy-4 align-items-center"><div class="col-lg-7"><span class="eyebrow">Employabilité</span><h2 class="section-title">Renforcer le lien entre formation et emploi</h2><div class="cms-rich-text"><p>Ce réseau constitue un atout majeur pour l’employabilité des étudiants, en leur donnant accès à des expériences professionnelles variées et à une meilleure compréhension des attentes des employeurs.</p><p>L’INSG poursuit le développement de cette dynamique afin d’accroître le nombre de stages effectifs, d’améliorer le placement des étudiants et de renforcer le suivi de l’insertion professionnelle des diplômés.</p></div></div><div class="col-lg-5"><div class="info-card"><div class="icon-box"><i class="bi bi-graph-up-arrow"></i></div><h3 class="h4">Une ambition durable</h3><p class="text-muted-insg mb-0">Consolider les partenariats et l’adéquation entre les formations, les compétences acquises et les besoins du monde professionnel.</p></div></div></div></div></section>

<section class="section bg-navy"><div class="container"><div class="cta-banner text-center"><span class="eyebrow justify-content-center">Collaborer avec l’INSG</span><h2 class="section-title text-white">Construisons ensemble les compétences de demain</h2><p class="text-white-50 mb-4">Stages, emplois, conférences, projets pédagogiques ou recherche : développons un partenariat adapté à vos objectifs.</p><a href="{{ route('pages.contact') }}" class="btn btn-insg-primary">Nous contacter</a></div></div></section>

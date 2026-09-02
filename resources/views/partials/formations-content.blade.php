<section class="section">
  <div class="container">
    <div class="row justify-content-center text-center section-heading">
      <div class="col-lg-9">
        <span class="eyebrow justify-content-center">Offre académique</span>
        <h2 class="section-title">Des parcours adaptés à chaque projet</h2>
        <p class="section-lead mx-auto">L’INSG déploie une offre structurée autour de la formation initiale et de la formation continue. Ces deux voies associent excellence académique, professionnalisation et adaptation aux besoins des organisations.</p>
        <div class="formation-quick-links"><a href="#formation-initiale"><i class="bi bi-mortarboard"></i> Formation initiale</a><a href="#formation-continue"><i class="bi bi-briefcase"></i> Formation continue</a></div>
      </div>
    </div>
  </div>
</section>

<section class="section bg-light-gray" id="formation-initiale">
  <div class="container">
    <div class="row section-heading">
      <div class="col-lg-9">
        <span class="eyebrow">Parcours académique</span>
        <h2 class="section-title">Formation initiale</h2>
        <p class="section-lead">La formation initiale constitue la mission centrale de l’INSG en tant qu’établissement public. Elle prépare les étudiants à exercer dans les principaux domaines de la gestion, du commerce, de la finance et du management.</p>
      </div>
    </div>
    <div class="formation-accordion accordion" id="initialeAccordion">
      @foreach([
        ['bts-initial', 'bi-journal-bookmark', 'BTS', '5 parcours', ['Comptabilité et Gestion des Organisations (CGO)', 'Action Commerciale (AC)', 'Commerce International (CI)', 'Négoce et Commerce du Bois', 'Délégué Médical']],
        ['licence-fondamentale', 'bi-mortarboard', 'Licence fondamentale', '3 parcours', ['Sciences de Gestion (LSG)', 'Finance Comptabilité (FC)', 'Marketing Commerce International (MCI)']],
        ['licence-professionnelle', 'bi-briefcase', 'Licence professionnelle', '12 parcours', ['Comptabilité Contrôle Audit (CCA)', 'Banque Finance (BF)', 'Assistant Ressources Humaines (ARH)', 'Gestion Touristique et Environnementale (GTE)', 'Gestion des Ressources', 'Management et Communication Commerciale (MCC)', 'Management des Opérations Internationales (MOI)', 'Informatique de Gestion (IG)', 'Gestion Économie Mines et Pétrole (GEMP)', 'Économie et Gestion des Structures Aéroportuaires', 'Management et Entrepreneuriat en Agro-industrie', 'Achat Logistique Transport']],
        ['master-professionnel', 'bi-award', 'Master professionnel', '5 parcours', ['Finance (FI)', 'Comptabilité Contrôle Audit (CCA)', 'Management des Affaires Internationales (MIA)', 'Management des Stratégies Commerciales (MSC)', 'Informatique et Gestion (IC)']],
        ['master-recherche', 'bi-search', 'Master recherche', '1 parcours', ['Sciences de Gestion (MRSG)']],
      ] as $index => [$id, $icon, $title, $count, $courses])
        <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $index ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}" aria-expanded="{{ $index ? 'false' : 'true' }}" aria-controls="{{ $id }}"><span class="formation-accordion-icon"><i class="bi {{ $icon }}"></i></span><span class="flex-grow-1">{{ $title }}</span><span class="formation-count">{{ $count }}</span></button></h3><div id="{{ $id }}" class="accordion-collapse collapse {{ $index ? '' : 'show' }}" data-bs-parent="#initialeAccordion"><div class="accordion-body"><ul class="formation-list mb-0">@foreach($courses as $course)<li><i class="bi bi-check2-circle"></i><span>{{ $course }}</span></li>@endforeach</ul>@if($id === 'master-recherche')<p class="formation-note mb-0">Un parcours consacré à l’approfondissement scientifique, aux méthodes de recherche et à la production de connaissances en sciences de gestion.</p>@endif</div></div></div>
      @endforeach
    </div>
  </div>
</section>

<section class="section">
  <div class="container"><div class="row gy-5 align-items-center">
    <div class="col-lg-7"><span class="eyebrow">Coopération académique</span><h2 class="section-title">Des formations développées en partenariat</h2><div class="cms-rich-text"><p>Dans une perspective de diversification de son offre et de renforcement de la qualité scientifique, l’INSG développe plusieurs formations avec des partenaires académiques et institutionnels.</p><p>Le Master Recherche en Sciences de Gestion est proposé en partenariat avec l’Université Omar Bongo. La Licence Gestion Économie Mines et Pétrole (GEMP) résulte d’un partenariat académique et financier avec l’Université des Sciences et Techniques de Masuku (USTM) et l’Agence Nationale des Bourses et Stages (ANBG).</p><p>La Licence GEMP et la Licence Informatique de Gestion sont notamment accessibles aux titulaires d’un baccalauréat scientifique.</p></div></div>
    <div class="col-lg-5"><div class="info-card"><div class="icon-box"><i class="bi bi-people"></i></div><h3 class="h4">Une ouverture au service de la qualité</h3><p class="mb-0 text-muted-insg">Ces coopérations favorisent le partage d’expertise, l’innovation pédagogique et la construction de parcours adaptés aux enjeux économiques du Gabon.</p></div></div>
  </div></div>
</section>

<section class="section bg-light-gray" id="formation-continue">
  <div class="container">
    <div class="row section-heading"><div class="col-lg-9"><span class="eyebrow">Développement professionnel</span><h2 class="section-title">Formation continue</h2><p class="section-lead">La formation continue s’adresse aux salariés, aux demandeurs d’emploi et à toute personne souhaitant renforcer, actualiser ou réorienter ses compétences. Elle comprend des parcours diplômants et un programme de perfectionnement professionnel dispensé en cours du soir.</p></div></div>
    <div class="formation-accordion accordion" id="continueAccordion">
      @foreach([
        ['bts-continu', 'bi-journal-check', 'BTS', '3 parcours', ['Comptabilité et Gestion des Organisations (CGO)', 'Action Commerciale (AC)', 'Commerce International (CI)']],
        ['licence-pro-continue', 'bi-briefcase', 'Licence professionnelle', '2 parcours', ['Logistique et Gestion Commerciale (LGM)', 'Gestion Financière et Comptable (GFC)']],
        ['master-pro-continu', 'bi-award', 'Master professionnel', '6 parcours', ['Finance et Actuariat (FA)', 'Gouvernance et Management Public (GOMAP)', 'Audit et Contrôle de Gestion (ACG)', 'Gestion des Entreprises (GE)', 'Management des Ressources Humaines (MRH)', 'Management Environnemental et Développement Durable (MEDD)']],
      ] as $index => [$id, $icon, $title, $count, $courses])
        <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $index ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}" aria-expanded="{{ $index ? 'false' : 'true' }}" aria-controls="{{ $id }}"><span class="formation-accordion-icon"><i class="bi {{ $icon }}"></i></span><span class="flex-grow-1">{{ $title }}</span><span class="formation-count">{{ $count }}</span></button></h3><div id="{{ $id }}" class="accordion-collapse collapse {{ $index ? '' : 'show' }}" data-bs-parent="#continueAccordion"><div class="accordion-body"><ul class="formation-list mb-0">@foreach($courses as $course)<li><i class="bi bi-check2-circle"></i><span>{{ $course }}</span></li>@endforeach</ul></div></div></div>
      @endforeach
    </div>
  </div>
</section>

<section class="section">
  <div class="container"><div class="row justify-content-center"><div class="col-lg-10"><div class="cms-rich-text text-center"><span class="eyebrow justify-content-center">Une voie accessible et évolutive</span><h2 class="section-title">Développer ses compétences à chaque étape de son parcours</h2><p>Structurée autour du BTS, de la Licence professionnelle et du Master professionnel, la formation continue permet une progression cohérente dans l’acquisition des compétences.</p><p>Initialement pensée pour les adultes en activité ou en transition professionnelle, elle accueille également un nombre croissant de jeunes bacheliers. Elle constitue ainsi une voie complémentaire d’accès à l’enseignement supérieur, tout en conservant sa vocation première de développement et de perfectionnement professionnels.</p></div></div></div></div>
</section>

<section class="section bg-navy"><div class="container"><div class="cta-banner text-center"><span class="eyebrow justify-content-center">Construisez votre parcours</span><h2 class="section-title text-white">Rejoignez une formation adaptée à votre projet</h2><p class="text-white-50 mb-4">Découvrez les conditions d’admission et préparez votre candidature à l’INSG.</p><a href="{{ route('pages.admissions') }}" class="btn btn-insg-primary">Découvrir les admissions</a></div></div></section>

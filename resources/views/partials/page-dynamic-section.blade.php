<section class="section{{ $background }}" data-cms-section="{{ $section->key }}">
  <div class="container">
    @if($section->title || $section->body || $section->eyebrow)
      <div class="row justify-content-center text-center section-heading"><div class="col-lg-8">
        @if($section->eyebrow)<span class="eyebrow justify-content-center">{{ $section->eyebrow }}</span>@endif
        @if($section->title)<h2 class="section-title">{{ $section->title }}</h2>@endif
        @if($section->body)<div class="section-lead mx-auto">{!! $section->body !!}</div>@endif
      </div></div>
    @endif

    @if($section->type === 'programs')
      <div class="row g-4">@forelse($programs ?? [] as $program)<div class="col-md-6 col-lg-4"><article class="formation-card h-100"><div class="icon-box"><i class="bi {{ $program->icon }}"></i></div><h3>{{ $program->name }}</h3><p>{{ $program->description }}</p><p class="small text-muted-insg"><strong>{{ $program->level }}</strong> · {{ $program->duration }}</p>@if($program->opportunities)<p class="small mb-0"><strong>Débouchés :</strong> {{ implode(', ', $program->opportunities) }}</p>@endif</article></div>@empty<p class="text-center text-muted">Aucune formation publiée.</p>@endforelse</div>
    @elseif($section->type === 'articles')
      <div class="row g-4">@forelse($articles ?? [] as $article)<div class="col-md-6 col-lg-4"><article class="news-card h-100"><div class="news-thumb"><span class="news-category">{{ $article->category }}</span>@if($article->image_url)<img src="{{ $article->image_url }}" alt="{{ $article->title }}">@endif</div><div class="news-body"><div class="news-meta"><i class="bi bi-calendar3"></i> {{ $article->published_at?->locale('fr')->translatedFormat('d F Y') }}</div><h3>{{ $article->title }}</h3><p>{{ $article->excerpt }}</p>@if($article->content)<button class="btn btn-link card-link p-0" data-bs-toggle="collapse" data-bs-target="#article-{{ $article->id }}">Lire la suite</button><div class="collapse mt-3" id="article-{{ $article->id }}">{!! nl2br(e($article->content)) !!}</div>@endif</div></article></div>@empty<p class="text-center text-muted">Aucune actualité publiée.</p>@endforelse</div>
    @elseif($section->type === 'announcements')
      <div class="row g-4">@forelse($announcements ?? [] as $announcement)<div class="col-md-6 col-lg-4"><article class="info-card h-100"><span class="badge-status {{ $announcement->status === 'open' ? 'success' : 'info' }}">{{ $announcement->type }}</span><h3 class="h5 mt-3">{{ $announcement->title }}</h3><p class="text-muted-insg">{{ $announcement->description }}</p><div class="small"><i class="bi bi-calendar3 me-1"></i>Publié le {{ $announcement->published_at->format('d/m/Y') }}@if($announcement->deadline_at)<br><strong>Date limite : {{ $announcement->deadline_at->format('d/m/Y') }}</strong>@endif</div></article></div>@empty<p class="text-center text-muted">Aucune annonce publiée.</p>@endforelse</div>
    @elseif($section->type === 'events')
      <div class="row g-4">@forelse($events ?? [] as $event)<div class="col-md-6 col-lg-4"><article class="info-card h-100"><div class="icon-box"><i class="bi bi-calendar-event"></i></div><h3 class="h5">{{ $event->title }}</h3>@if($event->description)<p class="text-muted-insg">{{ $event->description }}</p>@endif<p class="small mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }}</p><p class="small mb-0"><i class="bi bi-clock me-1"></i>{{ $event->starts_at->locale('fr')->translatedFormat('d F Y à H:i') }}</p></article></div>@empty<p class="text-center text-muted">Aucun événement publié.</p>@endforelse</div>
    @elseif($section->type === 'partners')
      <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-5 justify-content-center align-items-center">
        @forelse($partners ?? [] as $partner)
          <div class="col"><div class="partner-logo">
            @if($partner->website)<a href="{{ $partner->website }}" target="_blank" rel="noopener">@endif
            @if($partner->logo_url)<img src="{{ $partner->logo_url }}" alt="Logo {{ $partner->name }}">@else<strong>{{ $partner->name }}</strong>@endif
            @if($partner->website)</a>@endif
          </div></div>
        @empty
          <p class="text-center text-muted">Aucun partenaire publié.</p>
        @endforelse
      </div>
    @elseif($section->type === 'testimonials')
      <div class="row g-4 justify-content-center">@forelse($testimonials ?? [] as $testimonial)<div class="col-md-6 col-lg-4"><article class="testimonial-card h-100"><div class="d-flex align-items-center gap-3 mb-3">@if($testimonial->avatar_url)<img src="{{ $testimonial->avatar_url }}" width="58" height="58" class="rounded-circle object-fit-cover" alt="{{ $testimonial->name }}">@endif<div><strong>{{ $testimonial->name }}</strong><div class="small text-muted-insg">{{ $testimonial->role }}</div></div></div><blockquote class="mb-0">« {{ $testimonial->quote }} »</blockquote></article></div>@empty<p class="text-center text-muted">Aucun témoignage publié.</p>@endforelse</div>
    @elseif($section->type === 'statistics')
      <div class="row g-4 justify-content-center text-center">@forelse($statistics ?? [] as $statistic)<div class="col-6 col-lg-3"><div class="stat-card h-100"><div class="stat-number"><span data-counter="{{ $statistic->value }}">{{ $statistic->value }}</span>{{ $statistic->suffix }}</div><div class="stat-label">{{ $statistic->label }}</div></div></div>@empty<p class="text-center text-muted">Aucun chiffre clé publié.</p>@endforelse</div>
    @elseif($section->type === 'contact_form')
      @include('partials.forms.contact')
    @elseif($section->type === 'admission_form')
      @include('partials.forms.admission')
    @elseif($section->type === 'master_form')
      @include('partials.forms.master')
    @endif
  </div>
</section>

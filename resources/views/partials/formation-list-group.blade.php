<section class="formation-group" id="{{ $id }}" aria-labelledby="{{ $id }}-title">
  <header class="formation-group-heading">
    <span class="formation-group-icon"><i class="bi {{ $icon }}" aria-hidden="true"></i></span>
    <h3 id="{{ $id }}-title">{{ $title }}</h3>
    <span class="formation-count">{{ $count }}</span>
  </header>
  <ul class="formation-list mb-0">
    @foreach($courses as $course)
      <li><i class="bi bi-check2-circle" aria-hidden="true"></i><span>{{ $course }}</span></li>
    @endforeach
  </ul>
  @if($id === 'master-recherche')
    <p class="formation-note mb-0">Un parcours consacré à l’approfondissement scientifique, aux méthodes de recherche et à la production de connaissances en sciences de gestion.</p>
  @endif
</section>

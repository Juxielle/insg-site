@foreach (($sections ?? $page->sections) as $section)
  @php
    $background = $section->theme === 'gray' ? ' bg-light-gray' : ($section->theme === 'navy' ? ' bg-navy' : '');
    $textClass = $section->theme === 'navy' ? ' text-white' : '';
  @endphp
  @if (in_array($section->type, ['programs', 'articles', 'announcements', 'events', 'partners', 'testimonials', 'statistics', 'contact_form', 'admission_form', 'master_form']))
    @include('partials.page-dynamic-section', ['section' => $section, 'background' => $background])
  @elseif ($section->type === 'cta')
    <section class="section{{ $background }}" data-cms-section="{{ $section->key }}">
      <div class="container"><div class="cta-banner text-center">
        @if($section->eyebrow)<span class="eyebrow justify-content-center">{{ $section->eyebrow }}</span>@endif
        @if($section->title)<h2 class="section-title text-white">{{ $section->title }}</h2>@endif
        @if($section->body)<div class="text-white-50 mb-4">{!! $section->body !!}</div>@endif
        @if($section->button_label && $section->button_url)<a href="{{ $section->button_url }}" class="btn btn-insg-primary">{{ $section->button_label }}</a>@endif
      </div></div>
    </section>
  @elseif ($section->type === 'cards')
    <section class="section{{ $background }}" data-cms-section="{{ $section->key }}">
      <div class="container{{ $textClass }}">
        <div class="row justify-content-center text-center section-heading"><div class="col-lg-8">
          @if($section->eyebrow)<span class="eyebrow justify-content-center">{{ $section->eyebrow }}</span>@endif
          @if($section->title)<h2 class="section-title{{ $textClass }}">{{ $section->title }}</h2>@endif
          @if($section->body)<div class="section-lead mx-auto{{ $textClass }}">{!! $section->body !!}</div>@endif
        </div></div>
        <div class="row g-4 justify-content-center">
          @foreach($section->items ?? [] as $card)
            <div class="col-md-6 col-lg-4"><article class="info-card h-100">
              @if(filled($card['image'] ?? null))<img src="{{ $card['image'] }}" class="rounded-3 w-100 mb-3" style="height:210px;object-fit:cover" alt="{{ $card['title'] ?? '' }}">@endif
              @if(filled($card['icon'] ?? null))<div class="icon-box"><i class="bi {{ $card['icon'] }}"></i></div>@endif
              @if(filled($card['title'] ?? null))<h3 class="h5">{{ $card['title'] }}</h3>@endif
              @if(filled($card['text'] ?? null))<div class="text-muted-insg">{!! $card['text'] !!}</div>@endif
            </article></div>
          @endforeach
        </div>
      </div>
    </section>
  @else
    <section class="section{{ $background }}" data-cms-section="{{ $section->key }}">
      <div class="container{{ $textClass }}"><div class="row gy-5 align-items-center">
        <div class="{{ $section->image_url ? 'col-lg-7' : 'col-lg-9 mx-auto' }}">
          @if($section->eyebrow)<span class="eyebrow">{{ $section->eyebrow }}</span>@endif
          @if($section->title)<h2 class="section-title{{ $textClass }}">{{ $section->title }}</h2>@endif
          @if($section->body)<div class="cms-rich-text{{ $textClass }}">{!! $section->body !!}</div>@endif
          @if($section->button_label && $section->button_url)<a href="{{ $section->button_url }}" class="btn {{ $section->theme === 'navy' ? 'btn-insg-primary' : 'btn-insg-navy' }} mt-3">{{ $section->button_label }}</a>@endif
        </div>
        @if($section->image_url)<div class="col-lg-5"><img src="{{ $section->image_url }}" class="rounded-4 shadow w-100" alt="{{ $section->title }}"></div>@endif
      </div></div>
    </section>
  @endif
@endforeach

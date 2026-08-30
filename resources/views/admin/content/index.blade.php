@extends('admin.content.layout')
@section('title', $config['label'])
@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">{{ $config['label'] }}</h1><p class="text-muted mb-0">{{ $items->total() }} élément(s) enregistré(s).</p></div>
    <a href="{{ route('admin.content.create', $resource) }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Ajouter</a>
  </div>
  <div class="cms-card p-3 p-lg-4">
    <div class="table-responsive">
      <table class="table cms-table table-hover align-middle mb-0">
        <thead><tr>@foreach($config['list'] as $label)<th>{{ $label }}</th>@endforeach<th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse ($items as $item)
            <tr>
              @foreach($config['list'] as $field => $label)
                @php($value = data_get($item, $field))
                <td>
                  @if(is_bool($value) || in_array($field, $config['booleans'] ?? []))
                    <span class="badge {{ $value ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $value ? 'Oui' : 'Non' }}</span>
                  @elseif($value instanceof \Carbon\CarbonInterface)
                    {{ $value->format(str_contains($field, '_at') && !in_array($field, ['published_at', 'deadline_at']) ? 'd/m/Y H:i' : 'd/m/Y') }}
                  @elseif(in_array($field, ['logo_url', 'image_url', 'avatar_url']) && $value)
                    <img src="{{ $value }}" class="content-preview" alt="">
                  @else
                    {{ Str::limit((string) $value, 55) }}
                  @endif
                </td>
              @endforeach
              <td class="text-end text-nowrap">
                <a href="{{ route('admin.content.edit', [$resource, $item->id]) }}" class="btn btn-sm btn-outline-primary" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                <form class="d-inline" method="POST" action="{{ route('admin.content.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('Confirmer la suppression définitive ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Supprimer"><i class="bi bi-trash"></i></button></form>
              </td>
            </tr>
          @empty
            <tr><td colspan="{{ count($config['list']) + 1 }}" class="text-center text-muted py-5">Aucun contenu enregistré.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
  </div>
@endsection

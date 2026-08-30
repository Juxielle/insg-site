@extends('admin.content.layout')
@section('title', ($item ? 'Modifier' : 'Ajouter').' '.$config['singular'])
@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.content.index', $resource) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div><h1 class="h2 mb-1">{{ $item ? 'Modifier' : 'Ajouter' }} {{ strtolower($config['singular']) }}</h1><p class="text-muted mb-0">Les modifications seront visibles sur le site public.</p></div>
  </div>
  <form method="POST" enctype="multipart/form-data" action="{{ $item ? route('admin.content.update', [$resource, $item->id]) : route('admin.content.store', $resource) }}" class="cms-card cms-form p-4 p-lg-5">
    @csrf @if($item) @method('PUT') @endif
    <div class="row g-4">
      @foreach ($config['fields'] as $name => $field)
        @php
          $rawValue = old($name, $item?->{$name});
          if (in_array($name, $config['arrays'] ?? []) && is_array($rawValue)) $rawValue = implode("\n", $rawValue);
          if (in_array($name, $config['jsons'] ?? []) && is_array($rawValue)) $rawValue = json_encode($rawValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
          if ($rawValue instanceof \Carbon\CarbonInterface) $rawValue = $rawValue->format($field['type'] === 'datetime-local' ? 'Y-m-d\TH:i' : 'Y-m-d');
        @endphp
        <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
          @if($field['upload'] ?? false)
            <div class="cms-image-field">
              <label class="form-label" for="{{ $name }}_file">{{ $field['label'] }}</label>
              <input type="hidden" name="{{ $name }}" value="{{ $rawValue }}">
              <label class="cms-image-picker" for="{{ $name }}_file">
                <span class="cms-image-preview {{ $rawValue ? 'has-image' : '' }}">
                  <img src="{{ $rawValue ?: '' }}" alt="Aperçu de {{ strtolower($field['label']) }}" data-image-preview @if(!$rawValue) hidden @endif>
                  <span class="cms-image-placeholder" data-image-placeholder @if($rawValue) hidden @endif><i class="bi bi-image"></i><small>Aucune image sélectionnée</small></span>
                </span>
                <span class="cms-image-picker-action"><i class="bi bi-upload"></i><span data-image-label>{{ $rawValue ? 'Remplacer l’image' : 'Sélectionner une image' }}</span></span>
              </label>
              <input class="visually-hidden" type="file" id="{{ $name }}_file" name="{{ $name }}_file" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
              <small class="cms-field-help">JPG, PNG, WebP, GIF ou SVG — 5 Mo maximum.</small>
            </div>
          @elseif($field['type'] === 'checkbox')
            <div class="form-check form-switch mt-md-4 pt-md-2"><input type="hidden" name="{{ $name }}" value="0"><input class="form-check-input" type="checkbox" name="{{ $name }}" value="1" id="{{ $name }}" @checked(old($name, $item?->{$name} ?? false))><label class="form-check-label fw-semibold" for="{{ $name }}">{{ $field['label'] }}</label></div>
          @else
            <label class="form-label" for="{{ $name }}">{{ $field['label'] }}</label>
            @if($field['type'] === 'textarea')
              <textarea class="form-control" rows="5" id="{{ $name }}" name="{{ $name }}">{{ $rawValue }}</textarea>
            @elseif($field['type'] === 'select')
              <select class="form-select" id="{{ $name }}" name="{{ $name }}">@foreach($field['options'] as $optionValue => $optionLabel)<option value="{{ $optionValue }}" @selected((string)$rawValue === (string)$optionValue)>{{ $optionLabel }}</option>@endforeach</select>
            @else
              <input class="form-control" type="{{ $field['type'] }}" id="{{ $name }}" name="{{ $name }}" value="{{ $rawValue }}">
            @endif
          @endif
        </div>
      @endforeach
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top"><a href="{{ route('admin.content.index', $resource) }}" class="btn btn-light">Annuler</a><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-2"></i>Enregistrer</button></div>
  </form>
  <script>
    document.querySelectorAll('[data-image-input]').forEach((input) => {
      input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;
        const field = input.closest('.cms-image-field');
        const preview = field.querySelector('[data-image-preview]');
        const placeholder = field.querySelector('[data-image-placeholder]');
        const label = field.querySelector('[data-image-label]');
        const previousUrl = preview.dataset.objectUrl;
        if (previousUrl) URL.revokeObjectURL(previousUrl);
        const objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
        preview.dataset.objectUrl = objectUrl;
        preview.hidden = false;
        placeholder.hidden = true;
        label.textContent = file.name;
        field.querySelector('.cms-image-preview').classList.add('has-image');
      });
    });
  </script>
@endsection

<div class="row justify-content-center"><div class="col-xl-9"><div class="form-panel">
  @if(session('success') === 'contact')<div class="alert alert-success">Votre message a bien été transmis.</div>@endif
  <form method="POST" action="{{ route('contact.store') }}" class="needs-validation">@csrf
    <div class="row g-3"><div class="col-md-6"><label class="form-label">Nom complet</label><input class="form-control" name="nom" value="{{ old('nom') }}" required></div><div class="col-md-6"><label class="form-label">Adresse email</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div><div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control" name="telephone" value="{{ old('telephone') }}"></div><div class="col-md-6"><label class="form-label">Sujet</label><input class="form-control" name="sujet" value="{{ old('sujet') }}" required></div><div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="6" required>{{ old('message') }}</textarea></div></div>
    <button class="btn btn-insg-primary mt-4" type="submit"><i class="bi bi-send me-2"></i>Envoyer le message</button>
  </form>
</div></div></div>

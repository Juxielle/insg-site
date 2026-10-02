<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') | INSG Gabon</title>
  <meta name="robots" content="index,follow">
  <link rel="icon" href="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body style="--page-hero-image:url('{{ $siteMedia->get('inner_page_hero', '/assets/images/building.png') }}')">
  <a href="#main-content" class="skip-link">Aller au contenu principal</a>
  <header>
    <div class="univ-topbar d-none d-lg-block">
      <div class="container d-flex justify-content-between align-items-center">
        <div class="univ-topbar-profiles d-flex align-items-center gap-3">
          <span class="univ-topbar-label"><i class="bi bi-mortarboard me-1"></i> Profils :</span>
          <a href="{{ route('pages.admissions') }}">Lycéen & Candidat</a>
          <span class="sep">•</span>
          <a href="{{ route('pages.vie-etudiante') }}">Étudiant</a>
          <span class="sep">•</span>
          <a href="{{ route('pages.entreprises') }}">Entreprises</a>
        </div>
        <div class="univ-topbar-tools d-flex align-items-center gap-3">
          <a href="{{ route('pages.bibliotheque') }}"><i class="bi bi-book me-1"></i> Bibliothèque (BU)</a>
          <a href="{{ route('pages.contact') }}"><i class="bi bi-geo-alt me-1"></i> Contact</a>
        </div>
      </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-insg navbar-solid">
      <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
          <span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG"></span>
          <span class="brand-text">
            <span class="brand-republic">RÉPUBLIQUE GABONAISE</span>
            <span class="brand-title">{{ $siteSettings->get('institution_short_name', 'INSG') }}</span>
            <small class="brand-subtitle">{{ $siteSettings->get('institution_name', 'Institut National des Sciences de Gestion') }}</small>
          </span>
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#contestNav" aria-label="Menu"><i class="bi bi-list"></i></button>
        <div class="collapse navbar-collapse" id="contestNav">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Accueil</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('contests.index') ? 'active' : '' }}" href="{{ route('contests.index') }}">Concours</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('contests.results*') ? 'active' : '' }}" href="{{ route('contests.results') }}">Résultats</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('pages.formations') }}">Formations</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('pages.admissions') }}">Admissions</a></li>
          </ul>
          @include('partials.login-button')
        </div>
      </div>
    </nav>
  </header>
  <main id="main-content">
    @yield('content')
  </main>
  <footer class="footer-insg">
    <div class="container">
      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <span>&copy; {{ date('Y') }} {{ $siteSettings->get('copyright', 'INSG Gabon') }}</span>
        <div class="d-flex gap-3">
          <a href="{{ route('pages.contact') }}">Contact</a>
          <a href="#">Notice légale</a>
          <a href="#">Accessibilité</a>
        </div>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

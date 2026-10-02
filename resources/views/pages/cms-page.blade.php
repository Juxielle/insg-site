<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $page->meta_title }}</title><meta name="description" content="{{ $page->meta_description }}">
  <link rel="icon" href="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"><link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body style="--page-hero-image: url('{{ $siteMedia->get('inner_page_hero', '/assets/images/building.png') }}')">
  <a href="#main-content" class="skip-link">Aller au contenu principal</a>
  <header>
    <!-- Bandeau supérieur d'accès directs par profil (Style Lyon 3) -->
    <div class="univ-topbar d-none d-lg-block">
      <div class="container d-flex justify-content-between align-items-center">
        <div class="univ-topbar-profiles d-flex align-items-center gap-3">
          <span class="univ-topbar-label"><i class="bi bi-mortarboard me-1"></i> Profils :</span>
          <a href="{{ route('pages.admissions') }}">Lycéen & Candidat</a>
          <span class="sep">•</span>
          <a href="{{ route('pages.vie-etudiante') }}">Étudiant</a>
          <span class="sep">•</span>
          <a href="{{ route('pages.entreprises') }}">Entreprises & Partenaires</a>
          <span class="sep">•</span>
          <a href="{{ route('pages.international') }}">International</a>
          <span class="sep">•</span>
          <a href="{{ route('pages.recherche') }}">Recherche</a>
        </div>
        <div class="univ-topbar-tools d-flex align-items-center gap-3">
          <a href="{{ route('pages.bibliotheque') }}"><i class="bi bi-book me-1"></i> Bibliothèque (BU)</a>
          <a href="{{ route('contests.index') }}"><i class="bi bi-trophy me-1"></i> Concours</a>
          <a href="{{ route('pages.contact') }}"><i class="bi bi-geo-alt me-1"></i> Contact & Accès</a>
        </div>
      </div>
    </div>

    <!-- Navigation principale universitaire -->
    <nav class="navbar navbar-expand-lg navbar-insg navbar-solid">
      <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
          <span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span>
          <span class="brand-text">
            <span class="brand-republic">RÉPUBLIQUE GABONAISE</span>
            <span class="brand-title">{{ $siteSettings->get('institution_short_name', 'INSG') }}</span>
            <small class="brand-subtitle">{{ $siteSettings->get('institution_name', 'Institut National des Sciences de Gestion') }}</small>
          </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-label="Ouvrir le menu"><i class="bi bi-list"></i></button>
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav mx-auto align-items-lg-center">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('pages.about') ? 'active' : '' }}" href="{{ route('pages.about') }}">L'Institut</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('pages.formations', 'pages.admissions', 'pages.inscription-master', 'master.tracking', 'contests.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Formations</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ request()->routeIs('pages.formations') ? 'active' : '' }}" href="{{ route('pages.formations') }}"><i class="bi bi-mortarboard me-2"></i>Offre de formation</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.admissions') ? 'active' : '' }}" href="{{ route('pages.admissions') }}"><i class="bi bi-pencil-square me-2"></i>Admissions & Modalités</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('contests.index') ? 'active' : '' }}" href="{{ route('contests.index') }}"><i class="bi bi-trophy me-2"></i>Concours d'entrée</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.inscription-master') ? 'active' : '' }}" href="{{ route('pages.inscription-master') }}"><i class="bi bi-file-earmark-person me-2"></i>Candidature Master</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('master.tracking') ? 'active' : '' }}" href="{{ route('master.tracking') }}"><i class="bi bi-search me-2"></i>Suivi de candidature Master</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('pages.recherche', 'pages.incubateur', 'pages.entrepreneuriat', 'pages.international', 'pages.entreprises') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Recherche & Partenariats</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ request()->routeIs('pages.recherche') ? 'active' : '' }}" href="{{ route('pages.recherche') }}"><i class="bi bi-search me-2"></i>Laboratoire LARSG & Recherche</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.incubateur') ? 'active' : '' }}" href="{{ route('pages.incubateur') }}"><i class="bi bi-rocket-takeoff me-2"></i>Incubateur de projets</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.entrepreneuriat') ? 'active' : '' }}" href="{{ route('pages.entrepreneuriat') }}"><i class="bi bi-lightbulb me-2"></i>Entrepreneuriat étudiant</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.international') ? 'active' : '' }}" href="{{ route('pages.international') }}"><i class="bi bi-globe-americas me-2"></i>International & Coopération</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.entreprises') ? 'active' : '' }}" href="{{ route('pages.entreprises') }}"><i class="bi bi-briefcase me-2"></i>Espace Entreprises & Recrutement</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('pages.vie-etudiante', 'pages.bibliotheque', 'pages.actualites', 'pages.annonces-concours', 'contests.results*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Vie Universitaire</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ request()->routeIs('pages.vie-etudiante') ? 'active' : '' }}" href="{{ route('pages.vie-etudiante') }}"><i class="bi bi-people me-2"></i>Vie étudiante & Services</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.bibliotheque') ? 'active' : '' }}" href="{{ route('pages.bibliotheque') }}"><i class="bi bi-book me-2"></i>Bibliothèque Universitaire (BU)</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.actualites') ? 'active' : '' }}" href="{{ route('pages.actualites') }}"><i class="bi bi-newspaper me-2"></i>Actualités & Agenda</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('pages.annonces-concours') ? 'active' : '' }}" href="{{ route('pages.annonces-concours') }}"><i class="bi bi-megaphone me-2"></i>Annonces officielles</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('contests.results*') ? 'active' : '' }}" href="{{ route('contests.results') }}"><i class="bi bi-award me-2"></i>Résultats des concours</a></li>
              </ul>
            </li>
          </ul>
          @include('partials.login-button')
        </div>
      </div>
    </nav>
  </header>
  <main id="main-content">
    <section class="page-hero"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li><li class="breadcrumb-item active">{{ $page->name }}</li></ol></nav><h1>{{ $page->hero_title }}</h1>@if($page->hero_text)<p>{{ $page->hero_text }}</p>@endif</div></section>
    @if($page->slug === 'formations')
      @include('partials.formations-content')
    @elseif($page->slug === 'entreprises')
      @include('partials.partners-content')
    @else
      @include('partials.page-sections')
    @endif
  </main>
  <!-- ============ FOOTER INSTITUTIONNEL ============ -->
  <footer class="footer-insg">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="footer-brand">
            <span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span>
            <span>{{ $siteSettings->get('institution_short_name', 'INSG') }}</span>
          </div>
          <p>{{ $siteSettings->get('footer_description', 'L’Institut National des Sciences de Gestion forme les cadres dirigeants, managers et experts comptables du Gabon et d’Afrique centrale.') }}</p>
          <div class="footer-ministry-note small text-muted-insg mt-2">
            Sous la tutelle du Ministère de l'Enseignement Supérieur, de la Recherche Scientifique et de l'Innovation Technologique.
          </div>
          <div class="social-icons mt-3">
            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h5>L'Institut</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="{{ route('pages.about') }}">À propos</a></li>
            <li><a href="{{ route('pages.formations') }}">Formations</a></li>
            <li><a href="{{ route('pages.admissions') }}">Admissions</a></li>
            <li><a href="{{ route('pages.international') }}">International & Coopération</a></li>
            <li><a href="{{ route('pages.recherche') }}">Recherche & Labos</a></li>
            <li><a href="{{ route('pages.incubateur') }}">Incubateur</a></li>
            <li><a href="{{ route('pages.entrepreneuriat') }}">Entrepreneuriat</a></li>
            <li><a href="{{ route('pages.contact') }}">Contact & Accès</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-3">
          <h5>Services & Campus</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="{{ route('pages.vie-etudiante') }}">Vie étudiante & Campus</a></li>
            <li><a href="{{ route('pages.bibliotheque') }}">Bibliothèque Universitaire</a></li>
            <li><a href="{{ route('contests.index') }}">Concours d'entrée</a></li>
            <li><a href="{{ route('contests.results') }}">Résultats des concours</a></li>
            <li><a href="{{ route('pages.inscription-master') }}">Inscription Master</a></li>
            <li><a href="{{ route('master.tracking') }}">Suivi de candidature Master</a></li>
            <li><a href="{{ route('pages.entreprises') }}">Relations Entreprises</a></li>
          </ul>
        </div>
        <div class="col-lg-3">
          <h5>Coordonnées</h5>
          <ul class="list-unstyled footer-contact">
            <li><i class="bi bi-geo-alt"></i><span>{{ $siteSettings->get('address', 'B.P. 170, Libreville, Gabon') }}</span></li>
            <li><i class="bi bi-telephone"></i><span>{{ $siteSettings->get('phone', '+241 01 73 28 88') }}</span></li>
            <li><i class="bi bi-envelope"></i><span>{{ $siteSettings->get('email', 'contact@insg-gabon.ga') }}</span></li>
            <li><i class="bi bi-clock"></i><span>Lun. — Ven. : 07h30 – 16h30</span></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <span>&copy; {{ date('Y') }} {{ $siteSettings->get('copyright', 'Institut National des Sciences de Gestion — Tous droits réservés.') }}</span>
        <div class="d-flex gap-3">
          <a href="#">Mentions légales</a>
          <a href="#">Données personnelles</a>
          <a href="#">Accessibilité</a>
        </div>
      </div>
    </div>
  </footer>
  <button class="back-to-top" aria-label="Retour en haut"><i class="bi bi-arrow-up"></i></button><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="{{ asset('assets/js/app.js') }}"></script>

</body></html>

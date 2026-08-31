<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $page->meta_title }}</title><meta name="description" content="{{ $page->meta_description }}">
  <link rel="icon" href="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"><link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body style="--page-hero-image: url('{{ $siteMedia->get('inner_page_hero', '/assets/images/building.png') }}')">
  <a href="#main-content" class="skip-link">Aller au contenu principal</a>
  <header><nav class="navbar navbar-expand-lg navbar-insg navbar-solid"><div class="container">
    <a class="navbar-brand" href="{{ route('home') }}"><span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span><span>{{ $siteSettings->get('institution_short_name', 'INSG') }}<small>{{ $siteSettings->get('institution_name', 'Institut National des Sciences de Gestion') }}</small></span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-label="Ouvrir le menu"><i class="bi bi-list"></i></button>
    <div class="collapse navbar-collapse" id="mainNavbar"><ul class="navbar-nav mx-auto align-items-lg-center">
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('pages.about') ? 'active' : '' }}" href="{{ route('pages.about') }}">À propos</a></li>
      <li class="nav-item dropdown"><a class="nav-link dropdown-toggle {{ request()->routeIs('pages.formations', 'pages.admissions', 'pages.vie-etudiante', 'pages.recherche', 'pages.incubateur') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Académique</a><ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('pages.formations') ? 'active' : '' }}" href="{{ route('pages.formations') }}">Formations</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.admissions') ? 'active' : '' }}" href="{{ route('pages.admissions') }}">Admissions</a></li><li><a class="dropdown-item" href="{{ route('contests.index') }}">Concours</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.vie-etudiante') ? 'active' : '' }}" href="{{ route('pages.vie-etudiante') }}">Vie étudiante</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.recherche') ? 'active' : '' }}" href="{{ route('pages.recherche') }}">Recherche</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.incubateur') ? 'active' : '' }}" href="{{ route('pages.incubateur') }}">Incubateur</a></li>
      </ul></li>
      <li class="nav-item dropdown"><a class="nav-link dropdown-toggle {{ request()->routeIs('pages.actualites', 'pages.annonces-concours', 'pages.inscription-master', 'pages.bibliotheque', 'pages.entreprises') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Ressources</a><ul class="dropdown-menu">
        <li><a class="dropdown-item {{ request()->routeIs('pages.actualites') ? 'active' : '' }}" href="{{ route('pages.actualites') }}">Actualités</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.annonces-concours') ? 'active' : '' }}" href="{{ route('pages.annonces-concours') }}">Annonces et concours</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.inscription-master') ? 'active' : '' }}" href="{{ route('pages.inscription-master') }}">Inscription en Master</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.bibliotheque') ? 'active' : '' }}" href="{{ route('pages.bibliotheque') }}">Bibliothèque</a></li><li><a class="dropdown-item {{ request()->routeIs('pages.entreprises') ? 'active' : '' }}" href="{{ route('pages.entreprises') }}">Partenaires</a></li>
      </ul></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('pages.contact') ? 'active' : '' }}" href="{{ route('pages.contact') }}">Contact</a></li>
    </ul>@include('partials.login-button')</div>
  </div></nav></header>
  <main id="main-content">
    <section class="page-hero"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li><li class="breadcrumb-item active">{{ $page->name }}</li></ol></nav><h1>{{ $page->hero_title }}</h1>@if($page->hero_text)<p>{{ $page->hero_text }}</p>@endif</div></section>
    @include('partials.page-sections')
  </main>
  <footer class="footer-insg"><div class="container"><div class="row g-4"><div class="col-lg-5"><div class="footer-brand"><span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span><span>{{ $siteSettings->get('institution_short_name', 'INSG') }}</span></div><p>{{ $siteSettings->get('footer_description', '') }}</p></div><div class="col-6 col-lg-3"><h5>Navigation</h5><ul class="list-unstyled footer-links"><li><a href="{{ route('pages.about') }}">À propos</a></li><li><a href="{{ route('pages.formations') }}">Formations</a></li><li><a href="{{ route('pages.admissions') }}">Admissions</a></li><li><a href="{{ route('pages.contact') }}">Contact</a></li></ul></div><div class="col-6 col-lg-4"><h5>Ressources</h5><ul class="list-unstyled footer-links"><li><a href="{{ route('pages.actualites') }}">Actualités</a></li><li><a href="{{ route('pages.annonces-concours') }}">Annonces de concours</a></li><li><a href="{{ route('pages.bibliotheque') }}">Bibliothèque</a></li><li><a href="{{ route('login') }}">Administration</a></li></ul></div></div><div class="footer-bottom"><span>&copy; {{ date('Y') }} {{ $siteSettings->get('copyright', '') }}</span></div></div></footer>
  <button class="back-to-top" aria-label="Retour en haut"><i class="bi bi-arrow-up"></i></button><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="{{ asset('assets/js/app.js') }}"></script>
</body></html>

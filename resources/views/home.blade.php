<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $page->meta_title }}</title>
  <meta name="description" content="{{ $page->meta_description }}">
  <meta name="keywords" content="INSG, Gabon, école de gestion, management, finance, comptabilité, marketing, ressources humaines, banque, audit, Libreville">
  <meta name="author" content="INSG Gabon">
  <!-- Open Graph -->
  <meta property="og:title" content="INSG Gabon — Institut National des Sciences de Gestion">
  <meta property="og:description" content="Formez votre avenir avec l'excellence académique de l'INSG Gabon : finance, management, marketing, RH, banque, audit et informatique de gestion.">
  <meta property="og:type" content="website">
  <meta property="og:image" content="{{ asset(ltrim($siteMedia->get('social_cover', '/assets/images/insg1.webp'), '/')) }}">
  <link rel="canonical" href="https://www.insg-gabon.ga/">
  <!-- Favicon -->
  <link rel="icon" href="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- Styles INSG -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Aller au contenu principal</a>

  <!-- ============ NAVBAR ============ -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-insg">
      <div class="container">
        <a class="navbar-brand" href="index.html">
          <span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span>
          <span>{{ $siteSettings->get('institution_short_name', 'INSG') }}<small>{{ $siteSettings->get('institution_name', 'Institut National des Sciences de Gestion') }}</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Ouvrir le menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav mx-auto align-items-lg-center">
            <li class="nav-item"><a class="nav-link active" href="index.html">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="pages/about.html">À propos</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Académique</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="pages/formations.html"><i class="bi bi-mortarboard me-2"></i>Formations</a></li>
                <li><a class="dropdown-item" href="pages/admissions.html"><i class="bi bi-pencil-square me-2"></i>Admissions</a></li>
                <li><a class="dropdown-item" href="{{ route('contests.index') }}"><i class="bi bi-trophy me-2"></i>Concours</a></li>
                <li><a class="dropdown-item" href="pages/vie-etudiante.html"><i class="bi bi-people me-2"></i>Vie Étudiante</a></li>
                <li><a class="dropdown-item" href="pages/recherche.html"><i class="bi bi-search me-2"></i>Recherche</a></li>
                <li><a class="dropdown-item" href="pages/incubateur.html"><i class="bi bi-rocket-takeoff me-2"></i>Incubateurs</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Ressources</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="pages/actualites.html"><i class="bi bi-newspaper me-2"></i>Actualités</a></li>
                <li><a class="dropdown-item" href="pages/annonces-concours.html"><i class="bi bi-megaphone me-2"></i>Annonces de concours</a></li>
                <li><a class="dropdown-item" href="{{ route('contests.results') }}"><i class="bi bi-award me-2"></i>Résultats des concours</a></li>
                <li><a class="dropdown-item" href="pages/inscription-master.html"><i class="bi bi-file-earmark-person me-2"></i>Inscription en Master</a></li>
                <li><a class="dropdown-item" href="pages/bibliotheque.html"><i class="bi bi-book me-2"></i>Bibliothèque</a></li>
                <li><a class="dropdown-item" href="{{ route('pages.entreprises') }}"><i class="bi bi-briefcase me-2"></i>Partenaires</a></li>
              </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="pages/contact.html">Contact</a></li>
          </ul>
          @include('partials.login-button')
        </div>
      </div>
    </nav>
  </header>

  <main id="main-content">
    <!-- ============ 1. HERO ============ -->
    <section class="hero">
      <!-- Vidéo institutionnelle plein écran en fond -->
      <video class="hero-bg-video" autoplay muted loop playsinline poster="{{ $siteMedia->get('home_hero_poster', '/assets/images/insg1.webp') }}">
        <source src="assets/videos/insg-institutional-video.mp4" type="video/mp4">
      </video>
      <div class="hero-overlay"></div>

      <div class="container hero-content">
        <div class="row">
          <div class="col-lg-8 col-xl-7">
            <span class="hero-badge"><i class="bi bi-award"></i> Établissement de référence en sciences de gestion</span>
            <h1>{{ $page->hero_title }}</h1>
            <p class="lead">{{ $page->hero_text }}</p>
            <div class="d-flex flex-wrap gap-3 mt-4">
              <a href="pages/formations.html" class="btn btn-insg-primary btn-lg"><i class="bi bi-mortarboard me-2"></i>Découvrir nos formations</a>
              <a href="pages/admissions.html" class="btn btn-insg-outline btn-lg"><i class="bi bi-pencil-square me-2"></i>S'inscrire</a>
            </div>
            <div class="d-flex align-items-center gap-4 mt-5 flex-wrap">
              <div class="d-flex">
                <img src="{{ $siteMedia->get('home_student_1', '/assets/images/insg2.jpg') }}" class="rounded-circle border border-2 border-white object-fit-cover" width="40" height="40" alt="" style="margin-right:-12px;">
                <img src="{{ $siteMedia->get('home_student_2', '/assets/images/insg3.jpg') }}" class="rounded-circle border border-2 border-white object-fit-cover" width="40" height="40" alt="" style="margin-right:-12px;">
                <img src="{{ $siteMedia->get('home_student_3', '/assets/images/insg4.jpg') }}" class="rounded-circle border border-2 border-white object-fit-cover" width="40" height="40" alt="">
              </div>
              <span class="small hero-trust-text">Rejoint par plus de 8&nbsp;500 diplômés depuis la création de l'institut</span>
            </div>
          </div>
        </div>
      </div>
      <a href="#chiffres-cles" class="hero-scroll-cue" aria-label="Défiler vers le bas"><i class="bi bi-chevron-down"></i></a>
    </section>
    @if(($publishedContests ?? collect())->isNotEmpty())
      <section class="contest-results-banner"><div class="container"><div class="contest-results-panel"><div><span class="eyebrow"><i class="bi bi-award"></i> Publication officielle</span><h2>Résultats des concours disponibles</h2><p>Consultez de manière confidentielle les résultats publiés par l’INSG Gabon.</p></div><div class="d-flex flex-wrap gap-2">@foreach($publishedContests as $publishedContest)<a class="btn btn-insg-primary" href="{{ route('contests.results', ['contest' => $publishedContest->id]) }}">{{ $publishedContest->title }}<i class="bi bi-arrow-right ms-2"></i></a>@endforeach</div></div></div></section>
    @endif
    @include('partials.page-sections')

  </main>

  <!-- ============ FOOTER ============ -->
  <footer class="footer-insg">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="footer-brand">
            <span class="brand-badge"><img src="{{ $siteMedia->get('site_logo', '/assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span>
            <span>INSG</span>
          </div>
          <p>{{ $siteSettings->get('footer_description', 'L’Institut National des Sciences de Gestion forme les cadres et managers du Gabon.') }}</p>
          <div class="social-icons">
            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h5>Navigation</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="pages/about.html">À propos</a></li>
            <li><a href="pages/formations.html">Formations</a></li>
            <li><a href="pages/admissions.html">Admissions</a></li>
            <li><a href="pages/recherche.html">Recherche</a></li>
            <li><a href="pages/incubateur.html">Incubateurs</a></li>
            <li><a href="pages/actualites.html">Actualités</a></li>
            <li><a href="pages/contact.html">Contact</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h5>Ressources</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="pages/vie-etudiante.html">Vie étudiante</a></li>
            <li><a href="pages/bibliotheque.html">Bibliothèque</a></li>
            <li><a href="pages/entreprises.html">Partenaires</a></li>
            <li><a href="{{ route('login') }}">Connexion</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h5>Contact</h5>
          <ul class="list-unstyled footer-contact">
            <li><i class="bi bi-geo-alt"></i><span>{{ $siteSettings->get('address', 'Libreville, Gabon') }}</span></li>
            <li><i class="bi bi-telephone"></i><span>{{ $siteSettings->get('phone', '') }}</span></li>
            <li><i class="bi bi-envelope"></i><span>{{ $siteSettings->get('email', '') }}</span></li>
            <li><i class="bi bi-clock"></i><span>Lun. — Ven. : 07h30 – 17h00</span></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <span>&copy; {{ date('Y') }} {{ $siteSettings->get('copyright', 'Institut National des Sciences de Gestion — Tous droits réservés.') }}</span>
        <div class="d-flex gap-3">
          <a href="#">Mentions légales</a>
          <a href="#">Politique de confidentialité</a>
        </div>
      </div>
    </div>
  </footer>

  <button class="back-to-top" aria-label="Retour en haut de page"><i class="bi bi-arrow-up"></i></button>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>

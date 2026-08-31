<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') | Administration INSG</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/jpeg" href="{{ asset('assets/images/insg-logo.jpeg') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body class="cms-shell">
  <div class="cms-sidebar-overlay" data-cms-overlay></div>
  <aside class="cms-sidebar" id="cmsSidebar">
    <a href="{{ route('admin.content.dashboard') }}" class="cms-brand">
      <span class="cms-brand-mark"><img src="{{ asset('assets/images/insg-logo.jpeg') }}" alt="INSG"></span>
      <span><strong>INSG</strong><small>Administration du site</small></span>
    </a>
    <div class="cms-nav-label">Pilotage éditorial</div>
    <nav class="nav flex-column">
      <a href="{{ route('admin.content.dashboard') }}" class="nav-link {{ request()->routeIs('admin.content.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.content.dashboard')) aria-current="page" @endif><i class="bi bi-grid"></i>Vue d’ensemble</a>
      <a href="{{ route('admin.contests.index') }}" class="nav-link {{ request()->routeIs('admin.contests.*') ? 'active' : '' }}" @if(request()->routeIs('admin.contests.*')) aria-current="page" @endif><i class="bi bi-trophy"></i>Concours</a>
      <a href="{{ route('admin.content.submissions.index') }}" class="nav-link {{ request()->routeIs('admin.content.submissions.*') ? 'active' : '' }}" @if(request()->routeIs('admin.content.submissions.*')) aria-current="page" @endif><i class="bi bi-inbox"></i>Demandes reçues</a>
      @php
        $navigationGroups = [
          ['label' => 'Structure du site', 'icon' => 'bi-window-stack', 'items' => ['pages', 'page-sections']],
          ['label' => 'Académique', 'icon' => 'bi-mortarboard', 'items' => ['programs']],
          ['label' => 'Ressources', 'icon' => 'bi-folder2-open', 'items' => ['articles', 'events', 'announcements', 'partners']],
          ['label' => 'Institution', 'icon' => 'bi-building', 'items' => ['media', 'testimonials', 'statistics', 'settings']],
        ];
      @endphp
      @foreach ($navigationGroups as $groupIndex => $group)
        @php($groupIsActive = in_array($resource ?? null, $group['items'], true))
        <details class="cms-nav-group" @if($groupIsActive) open @endif>
          <summary class="cms-nav-parent {{ $groupIsActive ? 'active' : '' }}">
            <span class="cms-nav-parent-label"><i class="bi {{ $group['icon'] }}"></i>{{ $group['label'] }}</span>
            <i class="bi bi-chevron-down cms-nav-chevron" aria-hidden="true"></i>
          </summary>
          <div class="cms-submenu">
            @foreach ($group['items'] as $resourceKey)
              @continue(!isset($resources[$resourceKey]))
              @php($resourceConfig = $resources[$resourceKey])
              <a href="{{ route('admin.content.index', $resourceKey) }}" class="nav-link {{ ($resource ?? null) === $resourceKey ? 'active' : '' }}" @if(($resource ?? null) === $resourceKey) aria-current="page" @endif><i class="bi {{ $resourceConfig['icon'] }}"></i>{{ $resourceConfig['label'] }}</a>
            @endforeach
          </div>
        </details>
      @endforeach
    </nav>
    <div class="cms-sidebar-actions">
      <a href="{{ route('home') }}" target="_blank" class="nav-link"><i class="bi bi-box-arrow-up-right"></i>Voir le site</a>
      <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="nav-link border-0 bg-transparent w-100 text-start"><i class="bi bi-power"></i>Déconnexion</button></form>
    </div>
  </aside>

  <main class="cms-main">
    <header class="cms-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="cms-menu-toggle" type="button" data-cms-menu aria-controls="cmsSidebar" aria-expanded="false"><i class="bi bi-list"></i><span class="visually-hidden">Ouvrir le menu</span></button>
        <div><span class="cms-topbar-kicker">Espace sécurisé</span><strong>Gestion du site institutionnel</strong></div>
      </div>
      <div class="cms-user"><span class="cms-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="d-none d-sm-block"><strong>{{ auth()->user()->name }}</strong><small>Administrateur</small></span></div>
    </header>
    <div class="cms-content">
      @if (session('backoffice_success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('backoffice_success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
      @if ($errors->any())<div class="alert alert-danger"><strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs suivantes :</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
      @yield('content')
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (() => {
      const sidebar = document.getElementById('cmsSidebar');
      const toggle = document.querySelector('[data-cms-menu]');
      const overlay = document.querySelector('[data-cms-overlay]');
      const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        overlay.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
      };
      toggle?.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));
      overlay?.addEventListener('click', () => setOpen(false));
      document.querySelectorAll('.cms-sidebar a').forEach(link => link.addEventListener('click', () => { if (window.innerWidth < 992) setOpen(false); }));
    })();
  </script>
</body>
</html>

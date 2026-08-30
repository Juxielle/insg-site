<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion | INSG Gabon</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('assets/images/insg-logo.jpeg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="bg-light-gray">
  <main class="min-vh-100 d-flex align-items-center py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
          <div class="row g-0 bg-white rounded-4 shadow overflow-hidden">
            <div class="col-lg-5 p-4 p-lg-5 text-white d-flex flex-column justify-content-between" style="background:linear-gradient(145deg,#061a3a,#123a85);">
              <div>
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-3 text-white text-decoration-none mb-5">
                  <span class="brand-badge"><img src="{{ asset('assets/images/insg-logo.jpeg') }}" alt="Logo INSG Gabon"></span>
                  <span class="fs-4 fw-bold">INSG Gabon</span>
                </a>
                <span class="eyebrow text-white">Espace sécurisé</span>
                <h1 class="h2 mt-3">Bienvenue sur votre portail</h1>
                <p class="text-white-50">Consultez vos informations académiques et administratives depuis votre espace personnel.</p>
              </div>
              <a href="{{ route('home') }}" class="text-white text-decoration-none mt-5"><i class="bi bi-arrow-left me-2"></i>Retour au site</a>
            </div>
            <div class="col-lg-7 p-4 p-lg-5">
              <span class="eyebrow">Authentification</span>
              <h2 class="section-title mb-2">Se connecter</h2>
              <p class="text-muted-insg mb-4">Saisissez les identifiants fournis par l’administration.</p>

              @if ($errors->any())
                <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
              @endif

              <form method="POST" action="{{ route('login.store') }}" class="form-insg">
                @csrf
                <div class="mb-3">
                  <label for="email" class="form-label">Adresse email</label>
                  <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Mot de passe</label>
                  <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required></div>
                </div>
                <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label" for="remember">Rester connecté</label></div>
                <button type="submit" class="btn btn-insg-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Connexion</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>

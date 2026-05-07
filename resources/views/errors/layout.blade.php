<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Erreur' }} — {{ config('app.name') }}</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',Arial,sans-serif;background:#eef1f6;color:#1e293b;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:40px;max-width:480px;width:100%;text-align:center}
        .code{font-size:3.2rem;font-weight:800;color:#1a3a6e;line-height:1;margin-bottom:14px}
        h1{font-size:1.15rem;color:#1a3a6e;margin-bottom:10px}
        p{font-size:.9rem;color:#64748b;line-height:1.6;margin-bottom:24px}
        a{display:inline-block;background:#1a3a6e;color:#fff;text-decoration:none;padding:10px 22px;border-radius:7px;font-weight:600;font-size:.88rem}
        a:hover{background:#14316a}
    </style>
</head>
<body>
    <div class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/') }}">Retour à l'accueil</a>
    </div>
</body>
</html>

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('admin.password', '');

        if ($expected === '') {
            abort(503, 'Accès admin non configuré. Définissez ADMIN_PASSWORD dans .env.');
        }

        if ($request->session()->get('admin_authenticated') === true) {
            return $next($request);
        }

        if ($request->isMethod('POST') && $request->input('admin_password') !== null) {
            if (hash_equals($expected, (string) $request->input('admin_password'))) {
                $request->session()->regenerate();
                $request->session()->put('admin_authenticated', true);
                return redirect($request->fullUrl());
            }

            return response($this->loginPage('Mot de passe incorrect.'), 401)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return response($this->loginPage(), 401)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    private function loginPage(?string $error = null): string
    {
        $csrf  = csrf_token();
        $err   = $error ? '<div class="err">' . e($error) . '</div>' : '';
        $title = e(config('app.name', 'CNASS')) . ' — Accès Admin';

        return <<<HTML
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$title}</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Arial,sans-serif;background:#eef1f6;color:#1e293b;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.card{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:32px;max-width:420px;width:100%}
h1{font-size:1.15rem;color:#1a3a6e;margin-bottom:8px}
.lead{font-size:.85rem;color:#64748b;margin-bottom:24px;line-height:1.5}
label{font-size:.76rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:6px}
input[type=password]{border:1px solid #cbd5e1;border-radius:7px;padding:10px 13px;font-size:.9rem;width:100%;background:#fff}
input:focus{outline:none;border-color:#1a3a6e;box-shadow:0 0 0 3px rgba(26,58,110,.1)}
button{margin-top:18px;width:100%;background:#1a3a6e;color:#fff;border:none;border-radius:7px;padding:12px;font-weight:700;font-size:.9rem;cursor:pointer}
button:hover{background:#14316a}
.err{background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:10px 14px;border-radius:7px;font-size:.84rem;margin-bottom:16px}
</style>
</head>
<body>
<div class="card">
<h1>Accès administrateur</h1>
<p class="lead">Cette zone est réservée. Veuillez saisir le mot de passe administrateur.</p>
{$err}
<form method="POST" autocomplete="off">
<input type="hidden" name="_token" value="{$csrf}">
<label>Mot de passe</label>
<input type="password" name="admin_password" required autofocus>
<button type="submit">Se connecter</button>
</form>
</div>
</body>
</html>
HTML;
    }
}

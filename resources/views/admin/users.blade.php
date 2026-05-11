<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Utilisateurs (OTP)</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; }
        .topbar { background: #1a3a6e; color: #fff; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; }
        .topbar h1 { font-size: 1.2rem; }
        .topbar a, .topbar button { color: #fff; text-decoration: none; font-size: .85rem; background: rgba(255,255,255,.15); padding: 6px 14px; border-radius: 4px; border: none; cursor: pointer; }
        .topbar a:hover, .topbar button:hover { background: rgba(255,255,255,.25); }
        .content { padding: 32px; max-width: 1100px; margin: 0 auto; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.08); text-align: center; }
        .stat-card .value { font-size: 2rem; font-weight: bold; color: #1a3a6e; }
        .stat-card .value.warn { color: #b45309; }
        .stat-card .label { font-size: .8rem; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
        th { background: #1a3a6e; color: #fff; text-align: left; padding: 12px 16px; font-size: .85rem; }
        td { padding: 11px 16px; font-size: .85rem; border-bottom: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f0f4ff; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: .75rem; font-weight: 600; }
        .badge-submitted { background: #dcfce7; color: #15803d; }
        .badge-verified  { background: #fef3c7; color: #92400e; }
        .badge-pending   { background: #e2e8f0; color: #475569; }
        .phone { font-family: 'Segoe UI', monospace; }
    </style>
</head>
<body>
<div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
        <div style="width:36px;height:36px;background:#fff;border-radius:8px;padding:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <img src="{{ asset('logo.png') }}" alt="" style="width:100%;height:100%">
        </div>
        <h1>Utilisateurs ayant reçu un OTP</h1>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.index') }}">← Soumissions</a>
        <form method="POST" action="{{ route('admin.logout') }}" style="display:inline">
            @csrf
            <button type="submit">Déconnexion</button>
        </form>
    </div>
</div>

<div class="content">
    <div class="stats">
        <div class="stat-card">
            <div class="value">{{ $totals['all'] }}</div>
            <div class="label">Total destinataires</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $totals['verified'] }}</div>
            <div class="label">Vérifiés</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $totals['submitted'] }}</div>
            <div class="label">Soumissions</div>
        </div>
        <div class="stat-card">
            <div class="value warn">{{ $totals['not_submitted'] }}</div>
            <div class="label">Vérifiés mais non soumis</div>
        </div>
    </div>

    @if($employees->isEmpty())
        <p style="text-align:center;color:#999;padding:40px 0">Aucun envoi d'OTP enregistré.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>Téléphone</th>
                <th>Nom (si soumis)</th>
                <th>Statut</th>
                <th>OTP envoyés</th>
                <th>Dernier envoi</th>
                <th>Vérifié le</th>
            </tr>
        </thead>
        <tbody>
        @foreach($employees as $e)
            @php
                $hasSubmission = $submittedPhones->has($e->phone);
                if ($hasSubmission) {
                    $statusClass = 'badge-submitted';
                    $statusLabel = 'Soumis';
                } elseif ($e->verified_at) {
                    $statusClass = 'badge-verified';
                    $statusLabel = 'Vérifié, non soumis';
                } else {
                    $statusClass = 'badge-pending';
                    $statusLabel = 'OTP envoyé';
                }
            @endphp
            <tr>
                <td class="phone">{{ $e->phone }}</td>
                <td>{{ $submittedPhones[$e->phone] ?? '—' }}</td>
                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                <td>{{ $e->send_count }}</td>
                <td>{{ optional($e->last_sent_at)->format('d/m/Y H:i') ?? '—' }}</td>
                <td>{{ optional($e->verified_at)->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:18px">{{ $employees->links() }}</div>
    @endif
</div>
</body>
</html>

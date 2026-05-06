<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Soumissions CNASS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; }
        .topbar { background: #1a3a6e; color: #fff; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; }
        .topbar h1 { font-size: 1.2rem; }
        .topbar a { color: #fff; text-decoration: none; font-size: .85rem; background: rgba(255,255,255,.15); padding: 6px 14px; border-radius: 4px; }
        .topbar a:hover { background: rgba(255,255,255,.25); }
        .content { padding: 32px; max-width: 1100px; margin: 0 auto; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.08); text-align: center; }
        .stat-card .value { font-size: 2rem; font-weight: bold; color: #1a3a6e; }
        .stat-card .label { font-size: .8rem; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
        th { background: #1a3a6e; color: #fff; text-align: left; padding: 12px 16px; font-size: .85rem; }
        td { padding: 11px 16px; font-size: .85rem; border-bottom: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f0f4ff; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: .75rem; font-weight: 600; }
        .badge-marié   { background: #e3f2fd; color: #1565c0; }
        .badge-célibataire { background: #f3e5f5; color: #6a1b9a; }
        .badge-divorcé { background: #fff3e0; color: #e65100; }
        .badge-veuf    { background: #fce4ec; color: #880e4f; }
        .btn { display: inline-block; padding: 5px 12px; border-radius: 4px; font-size: .8rem; text-decoration: none; }
        .btn-view { background: #1a3a6e; color: #fff; }
        .btn-view:hover { background: #14316a; }
    </style>
</head>
<body>
<div class="topbar">
    <h1>Tableau de bord – Soumissions CNASS</h1>
    <a href="{{ route('admin.exportExcel') }}">Exporter Excel</a>
</div>

<div class="content">
    <div class="stats">
        <div class="stat-card">
            <div class="value">{{ $submissions->count() }}</div>
            <div class="label">Total soumissions</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $submissions->where('situation_familiale', 'marié(e)')->count() }}</div>
            <div class="label">Mariés</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $submissions->where('situation_familiale', 'célibataire')->count() }}</div>
            <div class="label">Célibataires</div>
        </div>
    </div>

    @if($submissions->isEmpty())
        <p style="text-align:center;color:#999;padding:40px 0">Aucune soumission pour le moment.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom complet</th>
                <th>Situation familiale</th>
                <th>Frères</th>
                <th>Sœurs</th>
                <th>Descendants</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($submissions as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->nom_complet }}</td>
                <td>
                    <span class="badge badge-{{ Str::before($s->situation_familiale, '(') }}">
                        {{ ucfirst($s->situation_familiale) }}
                    </span>
                </td>
                <td>{{ count($s->freres ?? []) }}</td>
                <td>{{ count($s->soeurs ?? []) }}</td>
                <td>{{ count($s->descendants ?? []) }}</td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td><a href="{{ route('admin.show', $s) }}" class="btn btn-view">Voir</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
</body>
</html>

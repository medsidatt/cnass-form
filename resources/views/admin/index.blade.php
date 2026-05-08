<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Soumissions CNASS</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
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

        /* Shareable form link */
        .share-card { background: #fff; border: 1px solid #bae6fd; border-left: 4px solid #0ea5e9; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; display: flex; gap: 14px; align-items: center; flex-wrap: wrap; box-shadow: 0 1px 6px rgba(0,0,0,.04); }
        .share-card-left { flex: 1; min-width: 240px; }
        .share-label { font-size: .72rem; color: #0369a1; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
        #share-url { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 9px 12px; font-size: .88rem; background: #f8fafc; color: #1e293b; font-family: 'Segoe UI', monospace; }
        #share-url:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.15); }
        .btn-copy { background: #0ea5e9; color: #fff; border: none; border-radius: 7px; padding: 11px 22px; font-size: .85rem; font-weight: 700; cursor: pointer; transition: background .15s; white-space: nowrap; min-width: 130px; }
        .btn-copy:hover { background: #0284c7; }
        .btn-copy.copied { background: #16a34a; }
    </style>
</head>
<body>
@php
    use App\Models\Submission;
    $totals = [
        'all'         => Submission::count(),
        'marié(e)'    => Submission::where('situation_familiale', 'marié(e)')->count(),
        'célibataire' => Submission::where('situation_familiale', 'célibataire')->count(),
    ];
@endphp
<div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
        <div style="width:36px;height:36px;background:#fff;border-radius:8px;padding:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <img src="{{ asset('logo.png') }}" alt="" style="width:100%;height:100%">
        </div>
        <h1>Tableau de bord – Soumissions CNASS</h1>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
        <a href="{{ route('admin.exportExcel') }}">Exporter Excel</a>
        <form method="POST" action="{{ route('admin.logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,.15);border:none;color:#fff;padding:6px 14px;border-radius:4px;font-size:.85rem;cursor:pointer">
                Déconnexion
            </button>
        </form>
    </div>
</div>

<div class="content">
    {{-- Shareable form link --}}
    <div class="share-card">
        <div class="share-card-left">
            <div class="share-label">Lien à partager avec les employés</div>
            <input type="text" id="share-url" value="{{ route('form') }}" readonly aria-label="URL du formulaire">
        </div>
        <button type="button" class="btn-copy" id="btn-copy" onclick="copyShareUrl()">
            <span id="btn-copy-label">Copier le lien</span>
        </button>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="value">{{ $totals['all'] }}</div>
            <div class="label">Total soumissions</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $totals['marié(e)'] }}</div>
            <div class="label">Mariés</div>
        </div>
        <div class="stat-card">
            <div class="value">{{ $totals['célibataire'] }}</div>
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
    <div style="margin-top:18px">{{ $submissions->links() }}</div>
    @endif
</div>

<script>
async function copyShareUrl() {
    const input = document.getElementById('share-url');
    const btn   = document.getElementById('btn-copy');
    const label = document.getElementById('btn-copy-label');
    const url   = input.value;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(url);
        } else {
            input.select();
            input.setSelectionRange(0, url.length);
            document.execCommand('copy');
            input.blur();
        }
        btn.classList.add('copied');
        label.textContent = '✓ Lien copié';
        setTimeout(() => { btn.classList.remove('copied'); label.textContent = 'Copier le lien'; }, 2200);
    } catch (e) {
        // Fallback: just select the field for manual copy.
        input.select();
        input.setSelectionRange(0, url.length);
        label.textContent = 'Ctrl+C pour copier';
    }
}
</script>
</body>
</html>

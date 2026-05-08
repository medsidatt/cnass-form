<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumission #{{ $submission->id }} – CNASS</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; }
        .topbar { background: #1a3a6e; color: #fff; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .topbar a.back { color: #fff; text-decoration: none; font-size: .85rem; opacity: .8; }
        .topbar a.back:hover { opacity: 1; }
        .topbar h1 { font-size: 1.1rem; }
        .btn-dl-admin { background: #fff; color: #1a3a6e; text-decoration: none; padding: 7px 18px; border-radius: 5px; font-size: .82rem; font-weight: 700; white-space: nowrap; }
        .btn-dl-admin:hover { background: #f0f4ff; }
        .content { padding: 32px; max-width: 900px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 6px rgba(0,0,0,.08); margin-bottom: 24px; overflow: hidden; }
        .card-header { background: #1a3a6e; color: #fff; padding: 12px 20px; font-size: .95rem; font-weight: bold; }
        .card-body { padding: 20px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field label { font-size: .75rem; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .field p { font-size: .9rem; margin-top: 2px; }
        .file-actions { display: flex; gap: 10px; margin-top: 4px; flex-wrap: wrap; }
        .file-actions a { font-size: .82rem; padding: 4px 12px; border-radius: 4px; text-decoration: none; font-weight: 600; }
        .btn-view { background: #f0f4ff; color: #1a3a6e; border: 1px solid #bfdbfe; }
        .btn-view:hover { background: #dbeafe; }
        .btn-download { background: #1a3a6e; color: #fff; }
        .btn-download:hover { background: #14316a; }
        .sub-card { border: 1px solid #e0e0e0; border-radius: 6px; padding: 14px; margin-bottom: 10px; }
        .sub-card strong { font-size: .85rem; color: #1a3a6e; }
        .sub-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px; }
        .empty { color: #aaa; font-size: .85rem; font-style: italic; }
        @media (max-width: 600px) { .row, .sub-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="topbar">
    <div class="topbar-left">
        <a href="{{ route('admin.index') }}" class="back">← Retour</a>
        <h1>{{ $submission->nom_complet }} — #{{ $submission->id }}</h1>
    </div>
    <a href="{{ route('admin.download', $submission) }}" class="btn-dl-admin">Télécharger la fiche Excel</a>
</div>

<div class="content">

    {{-- EMPLOYÉ --}}
    <div class="card">
        <div class="card-header">Employé</div>
        <div class="card-body">
            <div class="row">
                <div class="field"><label>Nom complet</label><p>{{ $submission->nom_complet }}</p></div>
                <div class="field"><label>Situation familiale</label><p>{{ ucfirst($submission->situation_familiale) }}</p></div>
                <div class="field">
                    <label>Carte d'identité</label>
                    @if($submission->ci_employe)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_employe']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_employe', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
                <div class="field">
                    <label>Photo</label>
                    @if($submission->photo_employe)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_employe']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_employe', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
            </div>
        </div>
    </div>

    {{-- PÈRE --}}
    <div class="card">
        <div class="card-header">Père</div>
        <div class="card-body">
            <div class="row">
                <div class="field"><label>Nom complet</label><p>{{ $submission->nom_pere ?? '—' }}</p></div>
                <div class="field"></div>
                <div class="field">
                    <label>Carte d'identité</label>
                    @if($submission->ci_pere)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_pere']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_pere', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
                <div class="field">
                    <label>Photo</label>
                    @if($submission->photo_pere)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_pere']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_pere', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MÈRE --}}
    <div class="card">
        <div class="card-header">Mère</div>
        <div class="card-body">
            <div class="row">
                <div class="field"><label>Nom complet</label><p>{{ $submission->nom_mere ?? '—' }}</p></div>
                <div class="field"></div>
                <div class="field">
                    <label>Carte d'identité</label>
                    @if($submission->ci_mere)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_mere']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_mere', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
                <div class="field">
                    <label>Photo</label>
                    @if($submission->photo_mere)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_mere']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_mere', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FRATRIE (Frères + Sœurs) --}}
    @php
        $freres = collect($submission->freres ?? [])->map(fn($f, $i) => array_merge($f, ['_type'=>'Frère', '_key'=>'freres', '_idx'=>$i]));
        $soeurs = collect($submission->soeurs ?? [])->map(fn($s, $i) => array_merge($s, ['_type'=>'Sœur',  '_key'=>'soeurs', '_idx'=>$i]));
        $fratrie = $freres->concat($soeurs)->values();
    @endphp
    <div class="card">
        <div class="card-header">Fratrie ({{ $fratrie->count() }} membre(s))</div>
        <div class="card-body">
            @forelse($fratrie as $i => $m)
                <div class="sub-card">
                    <strong>{{ $m['_type'] }} {{ $i + 1 }}{{ isset($m['nom']) && $m['nom'] ? ' – ' . $m['nom'] : '' }}</strong>
                    <div class="sub-row" style="margin-top:10px">
                        <div class="field">
                            <label>Carte d'identité</label>
                            @if(!empty($m['ci']))
                                <div class="file-actions">
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => $m['_key'].'.'.$m['_idx'].'.ci']) }}" target="_blank" class="btn-view">Voir</a>
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => $m['_key'].'.'.$m['_idx'].'.ci', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                                </div>
                            @else <span class="empty">—</span> @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            @if(!empty($m['photo']))
                                <div class="file-actions">
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => $m['_key'].'.'.$m['_idx'].'.photo']) }}" target="_blank" class="btn-view">Voir</a>
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => $m['_key'].'.'.$m['_idx'].'.photo', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                                </div>
                            @else <span class="empty">—</span> @endif
                        </div>
                    </div>
                </div>
            @empty
                <span class="empty">Aucun membre de la fratrie renseigné.</span>
            @endforelse
        </div>
    </div>

    {{-- CONJOINT --}}
    @if($submission->nom_conjoint || $submission->ci_conjoint || $submission->photo_conjoint)
    <div class="card">
        <div class="card-header">Conjoint(e)</div>
        <div class="card-body">
            <div class="row">
                <div class="field"><label>Nom complet</label><p>{{ $submission->nom_conjoint ?? '—' }}</p></div>
                <div class="field"></div>
                <div class="field">
                    <label>Carte d'identité</label>
                    @if($submission->ci_conjoint)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_conjoint']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'ci_conjoint', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
                <div class="field">
                    <label>Photo</label>
                    @if($submission->photo_conjoint)
                        <div class="file-actions">
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_conjoint']) }}" target="_blank" class="btn-view">Voir</a>
                            <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'photo_conjoint', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                        </div>
                    @else <span class="empty">—</span> @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- DESCENDANTS --}}
    <div class="card">
        <div class="card-header">Descendants ({{ count($submission->descendants ?? []) }})</div>
        <div class="card-body">
            @forelse($submission->descendants ?? [] as $i => $d)
                <div class="sub-card">
                    <strong>Descendant {{ $i + 1 }}{{ isset($d['nom']) && $d['nom'] ? ' – ' . $d['nom'] : '' }}</strong>
                    <div class="sub-row">
                        <div class="field">
                            <label>Carte d'identité</label>
                            @if(!empty($d['ci']))
                                <div class="file-actions">
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'descendants.'.$i.'.ci']) }}" target="_blank" class="btn-view">Voir</a>
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'descendants.'.$i.'.ci', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                                </div>
                            @else <span class="empty">—</span> @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            @if(!empty($d['photo']))
                                <div class="file-actions">
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'descendants.'.$i.'.photo']) }}" target="_blank" class="btn-view">Voir</a>
                                    <a href="{{ route('files.show', ['submission' => $submission, 'key' => 'descendants.'.$i.'.photo', 'download' => 1]) }}" class="btn-download">Télécharger</a>
                                </div>
                            @else <span class="empty">—</span> @endif
                        </div>
                    </div>
                </div>
            @empty
                <span class="empty">Aucun descendant renseigné.</span>
            @endforelse
        </div>
    </div>

    <p style="font-size:.8rem;color:#aaa;text-align:center">
        Soumis le {{ $submission->created_at->format('d/m/Y à H:i') }}
    </p>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche CNASS – Situation Familiale</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #eef1f6; color: #1e293b; min-height: 100vh; }

        .container { max-width: 880px; margin: 36px auto 64px; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }

        header { background: #1a3a6e; color: #fff; padding: 28px 40px; display: flex; align-items: center; gap: 18px; }
        header .logo { width: 46px; height: 46px; flex-shrink: 0; background: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 5px; }
        header .logo img { width: 100%; height: 100%; }
        header h1  { font-size: 1.2rem; font-weight: 700; }
        header p   { font-size: .78rem; opacity: .7; margin-top: 3px; }

        .body { padding: 40px; }

        /* Steps */
        .steps { display: flex; margin-bottom: 36px; }
        .step  { flex: 1; text-align: center; padding: 10px 4px 12px; font-size: .72rem; font-weight: 700; color: #94a3b8; border-bottom: 3px solid #e2e8f0; text-transform: uppercase; letter-spacing: .05em; }
        .step .num { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #e2e8f0; color: #64748b; font-size: .78rem; font-weight: 700; margin-bottom: 5px; }
        .step.active { color: #1a3a6e; border-bottom-color: #1a3a6e; }
        .step.active .num { background: #1a3a6e; color: #fff; }
        .step.done   { color: #16a34a; border-bottom-color: #16a34a; }
        .step.done   .num { background: #16a34a; color: #fff; }

        /* OTP panel */
        .otp-wrap { max-width: 440px; margin: 0 auto; }
        .otp-wrap h2  { font-size: 1.1rem; color: #1a3a6e; margin-bottom: 6px; font-weight: 700; }
        .otp-wrap .lead { font-size: .84rem; color: #64748b; line-height: 1.6; margin-bottom: 28px; }
        /* Form sections */
        .section { margin-bottom: 32px; }
        .section-title { font-size: .9rem; font-weight: 700; color: #1a3a6e; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .section-title span { background: #1a3a6e; color: #fff; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: .75rem; flex-shrink: 0; }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }

        /* Fields */
        .field { display: flex; flex-direction: column; gap: 5px; }
        label  { font-size: .76rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: .04em; }
        input[type="text"], input[type="tel"], select {
            border: 1px solid #cbd5e1; border-radius: 7px; padding: 10px 13px;
            font-size: .88rem; width: 100%; background: #fff; color: #1e293b;
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, select:focus { outline: none; border-color: #1a3a6e; box-shadow: 0 0 0 3px rgba(26,58,110,.1); }
        input[type="file"] {
            border: 1px dashed #cbd5e1; border-radius: 7px; padding: 7px 10px;
            font-size: .82rem; width: 100%; cursor: pointer; background: #f8fafc;
        }
        .file-existing { font-size: .74rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 5px; }
        .file-existing a { color: #1a3a6e; text-decoration: underline; }

        /* Member cards */
        .member-card { border: 1px solid #e2e8f0; border-radius: 9px; padding: 20px; margin-bottom: 14px; position: relative; background: #f8fafc; }
        .card-label  { font-size: .72rem; font-weight: 700; color: #1a3a6e; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 14px; }
        .remove-btn  { position: absolute; top: 14px; right: 14px; background: #fee2e2; color: #dc2626; border: none; border-radius: 5px; padding: 4px 12px; cursor: pointer; font-size: .75rem; font-weight: 600; }
        .remove-btn:hover { background: #fecaca; }
        .add-btn { background: #f0f4ff; color: #1a3a6e; border: 1px solid #bfdbfe; border-radius: 7px; padding: 9px 20px; cursor: pointer; font-size: .82rem; font-weight: 600; margin-top: 8px; transition: background .15s; }
        .add-btn:hover { background: #dbeafe; }

        /* Existing submission banner */
        .existing-banner { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 9px; padding: 16px 20px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .existing-banner .info strong { font-size: .92rem; color: #0c4a6e; }
        .existing-banner .info small  { display: block; font-size: .76rem; color: #0369a1; margin-top: 2px; }
        .existing-banner .dl-small    { background: #0ea5e9; color: #fff; text-decoration: none; padding: 8px 18px; border-radius: 6px; font-size: .8rem; font-weight: 600; white-space: nowrap; }
        .existing-banner .dl-small:hover { background: #0284c7; }

        /* Buttons */
        .btn  { display: inline-flex; align-items: center; justify-content: center; gap: 7px; border: none; border-radius: 7px; font-weight: 700; cursor: pointer; transition: background .15s, opacity .15s; }
        .btn:disabled { opacity: .55; cursor: not-allowed; }
        .btn-primary { background: #1a3a6e; color: #fff; padding: 11px 24px; font-size: .88rem; }
        .btn-primary:hover:not(:disabled) { background: #14316a; }
        .btn-primary.full { width: 100%; padding: 14px; font-size: .95rem; }
        .btn-green  { background: #16a34a; color: #fff; }
        .btn-green:hover:not(:disabled) { background: #15803d; }
        .btn-ghost  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 11px 24px; font-size: .88rem; }
        .btn-ghost:hover { background: #e2e8f0; }
        .btn-link   { background: none; border: none; color: #1a3a6e; font-size: .82rem; cursor: pointer; text-decoration: underline; padding: 0; }
        .btn-row    { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-phone  { background: #25d366; color: #fff; padding: 11px 22px; font-size: .88rem; }
        .btn-phone:hover:not(:disabled) { background: #1ebe59; }

        /* Inputs row */
        .input-row { display: flex; gap: 10px; }
        .input-row input { flex: 1; }
        .otp-input { letter-spacing: .35em; font-size: 1.1rem; text-align: center; }

        /* OTP info block (phone + expiry) */
        .otp-info { display: flex; align-items: stretch; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; padding: 14px 18px; margin-bottom: 22px; gap: 18px; }
        .otp-info-row { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
        .otp-info-icon { font-size: 1.3rem; flex-shrink: 0; }
        .otp-info-label { font-size: .68rem; color: #64748b; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; }
        .otp-info-value { font-size: .92rem; color: #1e293b; font-weight: 600; margin-top: 2px; font-variant-numeric: tabular-nums; }
        .otp-info-row.warn .otp-info-value { color: #d97706; }
        .otp-info-row.danger .otp-info-value { color: #dc2626; }
        .otp-info-divider { width: 1px; background: #e2e8f0; }
        @media (max-width: 480px) {
            .otp-info { flex-direction: column; gap: 12px; }
            .otp-info-divider { width: 100%; height: 1px; }
        }

        /* Resend block */
        .resend-block { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 9px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; }
        .resend-question { font-size: .82rem; color: #0369a1; }
        .btn-resend {
            position: relative;
            background: #fff; color: #1a3a6e;
            border: 1px solid #1a3a6e;
            padding: 10px 18px; border-radius: 7px;
            font-size: .85rem; font-weight: 600;
            cursor: pointer; overflow: hidden;
            display: inline-flex; align-items: center; justify-content: center;
            transition: background .15s, color .15s;
        }
        .btn-resend:not(:disabled):hover { background: #1a3a6e; color: #fff; }
        .btn-resend:disabled { color: #64748b; border-color: #cbd5e1; background: #f1f5f9; cursor: not-allowed; }
        .btn-resend > span:first-child { position: relative; z-index: 1; }
        .resend-progress {
            position: absolute; left: 0; bottom: 0;
            height: 3px; background: #1a3a6e;
            width: 0%;
            transition: width 1s linear;
        }

        /* Alerts */
        .alert { padding: 12px 16px; border-radius: 7px; margin-bottom: 16px; font-size: .84rem; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #86efac; }

        /* View panel (read-only employee info) */
        .view-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap; padding-bottom: 18px; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; }
        .view-header h2 { font-size: 1.25rem; color: #1a3a6e; margin-bottom: 4px; }
        .view-header p  { font-size: .78rem; color: #94a3b8; }
        .view-section { margin-bottom: 26px; }
        .view-section h3 { font-size: .82rem; font-weight: 700; color: #1a3a6e; text-transform: uppercase; letter-spacing: .05em; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; margin-bottom: 14px; }
        .view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px; }
        .view-field label { font-size: .7rem; color: #64748b; text-transform: uppercase; letter-spacing: .04em; font-weight: 600; display: block; margin-bottom: 3px; }
        .view-field > div { font-size: .9rem; color: #1e293b; }
        .view-file { display: inline-block; font-size: .82rem; color: #1a3a6e; text-decoration: none; background: #f0f4ff; border: 1px solid #bfdbfe; padding: 5px 12px; border-radius: 6px; font-weight: 600; }
        .view-file:hover { background: #dbeafe; }
        .view-empty { font-size: .82rem; color: #94a3b8; font-style: italic; }
        .view-subcard { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; padding: 14px 16px; margin-bottom: 10px; }
        .view-subcard strong { font-size: .85rem; color: #1a3a6e; }
        @media (max-width: 600px) { .view-grid { grid-template-columns: 1fr; } }

        /* Download / success panel */
        .success-card { text-align: center; padding: 24px 0; }
        .success-icon {
            width: 72px; height: 72px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: #16a34a;
            color: #fff;
            font-size: 2.4rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(22,163,74,.25);
        }
        .success-card h2 { font-size: 1.35rem; color: #16a34a; margin-bottom: 6px; }
        .success-card p  { color: #64748b; font-size: .88rem; margin-bottom: 28px; }
        .success-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-dl { background: #1a3a6e; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: .92rem; }
        .btn-dl:hover { background: #14316a; }
        .btn-modify { background: #f8fafc; color: #1a3a6e; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: .92rem; border: 1px solid #cbd5e1; cursor: pointer; }
        .btn-modify:hover { background: #f1f5f9; }

        .hidden { display: none !important; }
        #conjoint-section { display: none; }

        @media (max-width: 640px) {
            .body { padding: 24px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .span-2, .span-3 { grid-column: span 1; }
            header { padding: 20px 24px; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="logo"><img src="{{ asset('logo.png') }}" alt="AMC Travaux"></div>
        <div>
            <h1>Fiche de Situation Familiale</h1>
            <p>Caisse Nationale des Assurances Sociales des Salariés – CNASS</p>
        </div>
    </header>

    <div class="body">

        {{-- ══════════════════════════════════════════
             PANEL 1 – WhatsApp Verification
        ══════════════════════════════════════════ --}}
        <div id="panel-verify" class="{{ $phoneVerified ? 'hidden' : '' }}">
            <div class="steps">
                <div class="step active"><div class="num">1</div>Vérification</div>
                <div class="step"><div class="num">2</div>Formulaire</div>
                <div class="step"><div class="num">3</div>Confirmation</div>
            </div>

            <div class="otp-wrap">
                <h2>Authentification WhatsApp</h2>
                <p class="lead">Renseignez votre numéro WhatsApp pour accéder à votre fiche. Un code de vérification vous sera envoyé.</p>

                <div id="phone-step">
                    <div id="err-phone" class="alert alert-error hidden"></div>
                    <div class="field" style="margin-bottom:16px">
                        <label>Numéro WhatsApp</label>
                        <div class="input-row">
                            <div style="display:flex;align-items:stretch;flex:1">
                                <span style="background:#f1f5f9;border:1px solid #cbd5e1;border-right:none;border-radius:7px 0 0 7px;padding:10px 13px;font-size:.88rem;color:#475569;white-space:nowrap;display:flex;align-items:center;font-weight:600">+222</span>
                                <input type="tel" id="phone-input" placeholder="XXXXXXXX" autocomplete="tel" style="border-radius:0 7px 7px 0;border-left:none">
                            </div>
                            <button type="button" class="btn btn-phone" id="btn-send" onclick="sendOtp()">Envoyer le code</button>
                        </div>
                    </div>
                </div>

                <div id="otp-step" class="hidden">
                    <div id="err-otp" class="alert alert-error hidden"></div>

                    <div class="otp-info">
                        <div class="otp-info-row">
                            <span class="otp-info-icon" aria-hidden="true">📱</span>
                            <div>
                                <div class="otp-info-label">Numéro vérifié</div>
                                <div class="otp-info-value"><strong id="phone-display"></strong></div>
                            </div>
                        </div>
                        <div class="otp-info-divider"></div>
                        <div class="otp-info-row" id="otp-expiry">
                            <span class="otp-info-icon" aria-hidden="true">⏱️</span>
                            <div>
                                <div class="otp-info-label">Validité</div>
                                <div class="otp-info-value" id="otp-expiry-time">15:00</div>
                            </div>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom:18px">
                        <label>Code de vérification (6 chiffres)</label>
                        <div class="input-row">
                            <input type="text" id="otp-input" class="otp-input" maxlength="6" placeholder="· · · · · ·" inputmode="numeric" autocomplete="one-time-code">
                            <button type="button" class="btn btn-primary" id="btn-verify" onclick="verifyOtp()">Vérifier</button>
                        </div>
                    </div>

                    <div class="resend-block">
                        <span class="resend-question">Vous n'avez pas reçu le code&nbsp;?</span>
                        <button type="button" class="btn btn-resend" id="btn-resend" onclick="resendOtp()" disabled>
                            <span id="btn-resend-label">Renvoyer dans <span id="resend-countdown">60</span>s</span>
                            <span class="resend-progress" id="resend-progress"></span>
                        </button>
                    </div>

                    <button type="button" class="btn-link" onclick="resetPhone()" style="margin-top:18px">← Changer de numéro</button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             PANEL — VIEW MY INFO  (read-only landing for returning employees)
        ══════════════════════════════════════════ --}}
        @php $showView = $phoneVerified && $existing; @endphp
        <div id="panel-view" class="{{ $showView ? '' : 'hidden' }}">
            @if($existing)
            <div class="steps">
                <div class="step done"><div class="num">✓</div>Vérification</div>
                <div class="step done"><div class="num">✓</div>Formulaire</div>
                <div class="step done"><div class="num">✓</div>Confirmation</div>
            </div>

            <div id="saved-banner" class="alert alert-success hidden">
                ✓ Vos informations ont bien été enregistrées.
            </div>

            <div class="view-header">
                <div>
                    <h2>{{ $existing->nom_complet }}</h2>
                    <p>Dernière mise à jour : {{ $existing->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
                <button type="button" class="btn btn-primary" onclick="switchToForm()">Modifier ma fiche</button>
            </div>

            @php
                $situations = [
                    'célibataire' => 'Célibataire',
                    'marié(e)'    => 'Marié(e)',
                    'divorcé(e)'  => 'Divorcé(e)',
                    'veuf/veuve'  => 'Veuf / Veuve',
                ];
                $fileLink = fn($key) => route('files.show', ['submission' => $existing, 'key' => $key]);
            @endphp

            {{-- Employé --}}
            <div class="view-section">
                <h3>Informations de l'employé</h3>
                <div class="view-grid">
                    <div class="view-field"><label>Nom complet</label><div>{{ $existing->nom_complet }}</div></div>
                    <div class="view-field"><label>Téléphone</label><div>{{ $verifiedPhone }}</div></div>
                    <div class="view-field"><label>Situation familiale</label><div>{{ $situations[$existing->situation_familiale] ?? $existing->situation_familiale }}</div></div>
                    <div class="view-field"></div>
                    <div class="view-field"><label>Carte d'identité</label>
                        @if($existing->ci_employe)<a href="{{ $fileLink('ci_employe') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                    <div class="view-field"><label>Photo</label>
                        @if($existing->photo_employe)<a href="{{ $fileLink('photo_employe') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                </div>
            </div>

            {{-- Père --}}
            @if($existing->nom_pere || $existing->ci_pere || $existing->photo_pere)
            <div class="view-section">
                <h3>Père</h3>
                <div class="view-grid">
                    <div class="view-field" style="grid-column:span 2"><label>Nom complet</label><div>{{ $existing->nom_pere ?? '—' }}</div></div>
                    <div class="view-field"><label>Carte d'identité</label>
                        @if($existing->ci_pere)<a href="{{ $fileLink('ci_pere') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                    <div class="view-field"><label>Photo</label>
                        @if($existing->photo_pere)<a href="{{ $fileLink('photo_pere') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Mère --}}
            @if($existing->nom_mere || $existing->ci_mere || $existing->photo_mere)
            <div class="view-section">
                <h3>Mère</h3>
                <div class="view-grid">
                    <div class="view-field" style="grid-column:span 2"><label>Nom complet</label><div>{{ $existing->nom_mere ?? '—' }}</div></div>
                    <div class="view-field"><label>Carte d'identité</label>
                        @if($existing->ci_mere)<a href="{{ $fileLink('ci_mere') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                    <div class="view-field"><label>Photo</label>
                        @if($existing->photo_mere)<a href="{{ $fileLink('photo_mere') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Conjoint --}}
            @if($existing->nom_conjoint || $existing->ci_conjoint || $existing->photo_conjoint)
            <div class="view-section">
                <h3>Conjoint(e)</h3>
                <div class="view-grid">
                    <div class="view-field" style="grid-column:span 2"><label>Nom complet</label><div>{{ $existing->nom_conjoint ?? '—' }}</div></div>
                    <div class="view-field"><label>Carte d'identité</label>
                        @if($existing->ci_conjoint)<a href="{{ $fileLink('ci_conjoint') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                    <div class="view-field"><label>Photo</label>
                        @if($existing->photo_conjoint)<a href="{{ $fileLink('photo_conjoint') }}" target="_blank" class="view-file">Voir le fichier</a>
                        @else<span class="view-empty">Non renseigné</span>@endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Descendants --}}
            @if(!empty($existing->descendants))
            <div class="view-section">
                <h3>Descendants ({{ count($existing->descendants) }})</h3>
                @foreach($existing->descendants as $i => $d)
                <div class="view-subcard">
                    <strong>Descendant {{ $i + 1 }}@if(!empty($d['nom'])) — {{ $d['nom'] }}@endif</strong>
                    <div class="view-grid" style="margin-top:10px">
                        <div class="view-field"><label>Carte d'identité</label>
                            @if(!empty($d['ci']))<a href="{{ $fileLink('descendants.'.$i.'.ci') }}" target="_blank" class="view-file">Voir le fichier</a>
                            @else<span class="view-empty">Non renseigné</span>@endif
                        </div>
                        <div class="view-field"><label>Photo</label>
                            @if(!empty($d['photo']))<a href="{{ $fileLink('descendants.'.$i.'.photo') }}" target="_blank" class="view-file">Voir le fichier</a>
                            @else<span class="view-empty">Non renseigné</span>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Fratrie --}}
            @php
                $fratrie = collect($existing->freres ?? [])->map(fn($f, $i) => array_merge($f, ['_type'=>'Frère', '_key'=>'freres', '_idx'=>$i]))
                    ->concat(collect($existing->soeurs ?? [])->map(fn($s, $i) => array_merge($s, ['_type'=>'Sœur', '_key'=>'soeurs', '_idx'=>$i])))
                    ->values();
            @endphp
            @if($fratrie->isNotEmpty())
            <div class="view-section">
                <h3>Fratrie ({{ $fratrie->count() }})</h3>
                @foreach($fratrie as $i => $m)
                <div class="view-subcard">
                    <strong>{{ $m['_type'] }} {{ $i + 1 }}@if(!empty($m['nom'])) — {{ $m['nom'] }}@endif</strong>
                    <div class="view-grid" style="margin-top:10px">
                        <div class="view-field"><label>Carte d'identité</label>
                            @if(!empty($m['ci']))<a href="{{ $fileLink($m['_key'].'.'.$m['_idx'].'.ci') }}" target="_blank" class="view-file">Voir le fichier</a>
                            @else<span class="view-empty">Non renseigné</span>@endif
                        </div>
                        <div class="view-field"><label>Photo</label>
                            @if(!empty($m['photo']))<a href="{{ $fileLink($m['_key'].'.'.$m['_idx'].'.photo') }}" target="_blank" class="view-file">Voir le fichier</a>
                            @else<span class="view-empty">Non renseigné</span>@endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <button type="button" class="btn btn-primary btn-green full" onclick="switchToForm()" style="margin-top:14px">
                Modifier ma fiche
            </button>
            @endif
        </div>

        {{-- ══════════════════════════════════════════
             PANEL — Form
        ══════════════════════════════════════════ --}}
        <div id="panel-form" class="{{ $phoneVerified && !$existing ? '' : 'hidden' }}">

            <div class="steps">
                <div class="step done"><div class="num">✓</div>Vérification</div>
                <div class="step active"><div class="num">2</div>Formulaire</div>
                <div class="step"><div class="num">3</div>Confirmation</div>
            </div>

            {{-- Existing submission banner --}}
            @if($existing)
            <div class="existing-banner">
                <div class="info">
                    <strong>Fiche existante — {{ $existing->nom_complet }}</strong>
                    <small>Dernière mise à jour : {{ $existing->updated_at->format('d/m/Y à H:i') }}</small>
                </div>
            </div>
            @endif

            <div id="alert-form-error" class="alert alert-error hidden"></div>

            <form id="cnass-form" enctype="multipart/form-data">
                @csrf

                {{-- ── Employé ── --}}
                <div class="section">
                    <div class="section-title"><span>1</span> Informations de l'employé</div>
                    <div class="grid-2">
                        <div class="field span-2">
                            <label>Nom complet *</label>
                            <input type="text" name="nom_complet" required placeholder="Prénom et Nom"
                                   value="{{ $existing->nom_complet ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Situation familiale *</label>
                            <select name="situation_familiale" id="situation_familiale" required>
                                <option value="">— Sélectionner —</option>
                                @foreach(['célibataire'=>'Célibataire','marié(e)'=>'Marié(e)','divorcé(e)'=>'Divorcé(e)','veuf/veuve'=>'Veuf / Veuve'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ ($existing->situation_familiale ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field"></div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_employe" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->ci_employe))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'ci_employe']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_employe" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->photo_employe))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'photo_employe']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Père ── --}}
                <div class="section">
                    <div class="section-title"><span>2</span> Père</div>
                    <div class="grid-2">
                        <div class="field span-2">
                            <label>Nom complet</label>
                            <input type="text" name="nom_pere" placeholder="Prénom et Nom"
                                   value="{{ $existing->nom_pere ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_pere" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->ci_pere))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'ci_pere']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_pere" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->photo_pere))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'photo_pere']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Mère ── --}}
                <div class="section">
                    <div class="section-title"><span>3</span> Mère</div>
                    <div class="grid-2">
                        <div class="field span-2">
                            <label>Nom complet</label>
                            <input type="text" name="nom_mere" placeholder="Prénom et Nom"
                                   value="{{ $existing->nom_mere ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_mere" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->ci_mere))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'ci_mere']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_mere" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->photo_mere))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'photo_mere']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Conjoint(e) ── --}}
                <div class="section" id="conjoint-section">
                    <div class="section-title"><span>4</span> Conjoint(e)</div>
                    <div class="grid-2">
                        <div class="field span-2">
                            <label>Nom complet</label>
                            <input type="text" name="nom_conjoint" placeholder="Prénom et Nom"
                                   value="{{ $existing->nom_conjoint ?? '' }}">
                        </div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_conjoint" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->ci_conjoint))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'ci_conjoint']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_conjoint" accept=".jpg,.jpeg,.pdf">
                            @if(!empty($existing->photo_conjoint))
                                <div class="file-existing"><a href="{{ route('files.show', ['submission' => $existing, 'key' => 'photo_conjoint']) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Descendants ── --}}
                <div class="section">
                    <div class="section-title"><span>5</span> Descendants</div>
                    <input type="hidden" name="descendants_count" id="descendants_count" value="0">
                    <div id="descendants-container"></div>
                    <button type="button" class="add-btn" onclick="addDescendant()">+ Ajouter un descendant</button>
                </div>

                {{-- ── Fratrie ── --}}
                <div class="section">
                    <div class="section-title"><span>6</span> Fratrie (Frères &amp; Sœurs)</div>
                    <input type="hidden" name="fratrie_count" id="fratrie_count" value="0">
                    <div id="fratrie-container"></div>
                    <button type="button" class="add-btn" onclick="addFratrie()">+ Ajouter un(e) frère / sœur</button>
                </div>

                <button type="submit" class="btn btn-primary btn-green full" id="submit-btn">
                    {{ $existing ? 'Mettre à jour ma fiche' : 'Soumettre ma fiche' }}
                </button>
            </form>
        </div>


    </div>
</div>

{{-- Pre-fill data for JS --}}
@if($existing)
@php
    $withUrls = function (array $items, string $listKey) use ($existing) {
        return collect($items)->map(function ($item, $i) use ($existing, $listKey) {
            $item['_ci_url']    = !empty($item['ci'])
                ? route('files.show', ['submission' => $existing, 'key' => "$listKey.$i.ci"]) : '';
            $item['_photo_url'] = !empty($item['photo'])
                ? route('files.show', ['submission' => $existing, 'key' => "$listKey.$i.photo"]) : '';
            return $item;
        })->all();
    };
@endphp
<script>
window._prefill = {
    freres:      @json($withUrls($existing->freres      ?? [], 'freres')),
    soeurs:      @json($withUrls($existing->soeurs      ?? [], 'soeurs')),
    descendants: @json($withUrls($existing->descendants ?? [], 'descendants')),
};
</script>
@endif

<script>
const SEND_URL   = '{{ route("verify.send") }}';
const CHECK_URL  = '{{ route("verify.check") }}';
const SUBMIT_URL = '{{ route("submit") }}';
const CSRF       = '{{ csrf_token() }}';
let IS_UPDATE    = {{ $existing ? 'true' : 'false' }};

// ── Situation familiale ──────────────────────────────────────────────────────
function toggleConjoint() {
    const val = document.getElementById('situation_familiale').value;
    document.getElementById('conjoint-section').style.display = val === 'marié(e)' ? 'block' : 'none';
}
document.getElementById('situation_familiale').addEventListener('change', toggleConjoint);

// ── WhatsApp OTP ─────────────────────────────────────────────────────────────
const OTP_TTL_SECONDS    = 15 * 60;   // matches VerifyController::OTP_TTL_MINUTES
const RESEND_COOLDOWN_S  = 60;        // 1 minute between resends — well inside the 3/min server limit

let _expiryTimer = null;
let _resendTimer = null;

async function sendOtp() {
    let digits = document.getElementById('phone-input').value.trim().replace(/\s+/g, '');
    digits = digits.replace(/^\+?2{0,1}2{0,1}2{0,1}/, '').replace(/^0+/, '');
    const phone = digits;
    if (!phone) return;
    const btn = document.getElementById('btn-send');
    btn.disabled = true; btn.textContent = 'Envoi…';
    hideEl('err-phone');
    try {
        const data = await postJson(SEND_URL, { phone });
        if (data.success) {
            document.getElementById('phone-display').textContent = '+222' + phone;
            showEl('otp-step'); hideEl('phone-step');
            startOtpTimers();
        } else {
            showErr('err-phone', data.message);
        }
    } catch (e) { showErr('err-phone', e.message || 'Erreur réseau. Veuillez réessayer.'); }
    finally { btn.disabled = false; btn.textContent = 'Envoyer le code'; }
}

async function resendOtp() {
    const btn   = document.getElementById('btn-resend');
    const label = document.getElementById('btn-resend-label');
    if (btn.disabled) return;
    const phone = (document.getElementById('phone-display').textContent || '').replace(/^\+222\s*/, '').replace(/\s+/g, '');
    if (!phone) return;
    btn.disabled = true; label.textContent = 'Envoi…';
    hideEl('err-otp');
    try {
        const data = await postJson(SEND_URL, { phone });
        if (data.success) {
            startOtpTimers();
        } else {
            showErr('err-otp', data.message);
            startResendCountdown(); // re-arm cooldown so user can retry shortly
        }
    } catch (e) {
        showErr('err-otp', e.message || 'Erreur réseau. Veuillez réessayer.');
        startResendCountdown();
    }
}

async function verifyOtp() {
    const code = document.getElementById('otp-input').value.trim();
    if (code.length < 6) return;
    const btn = document.getElementById('btn-verify');
    btn.disabled = true; btn.textContent = 'Vérification…';
    hideEl('err-otp');
    try {
        const data = await postJson(CHECK_URL, { code });
        if (data.success) {
            stopOtpTimers();
            window.location.href = '{{ route("form") }}';
        } else {
            showErr('err-otp', data.message);
        }
    } catch (e) { showErr('err-otp', e.message || 'Erreur réseau. Veuillez réessayer.'); }
    finally { btn.disabled = false; btn.textContent = 'Vérifier'; }
}

function resetPhone() {
    stopOtpTimers();
    document.getElementById('otp-input').value = '';
    hideEl('err-otp'); showEl('phone-step'); hideEl('otp-step');
}

// ── OTP countdowns ───────────────────────────────────────────────────────────
function startOtpTimers() {
    startExpiryCountdown();
    startResendCountdown();
}
function stopOtpTimers() {
    if (_expiryTimer) { clearInterval(_expiryTimer); _expiryTimer = null; }
    if (_resendTimer) { clearInterval(_resendTimer); _resendTimer = null; }
}
function fmtMMSS(total) {
    const m = Math.floor(total / 60), s = total % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}
function startExpiryCountdown() {
    if (_expiryTimer) clearInterval(_expiryTimer);
    let remaining = OTP_TTL_SECONDS;
    const el     = document.getElementById('otp-expiry-time');
    const row    = document.getElementById('otp-expiry');
    const verify = document.getElementById('btn-verify');
    row.classList.remove('warn', 'danger');
    el.textContent = fmtMMSS(remaining);
    verify.disabled = false;
    _expiryTimer = setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
            clearInterval(_expiryTimer); _expiryTimer = null;
            el.textContent = 'Expiré';
            row.classList.remove('warn');
            row.classList.add('danger');
            verify.disabled = true;
            // Allow immediate resend once expired.
            unlockResend();
            return;
        }
        el.textContent = fmtMMSS(remaining);
        if (remaining <= 60) { row.classList.remove('warn'); row.classList.add('danger'); }
        else if (remaining <= 180) { row.classList.add('warn'); row.classList.remove('danger'); }
    }, 1000);
}
function startResendCountdown() {
    if (_resendTimer) clearInterval(_resendTimer);
    let remaining = RESEND_COOLDOWN_S;
    const btn      = document.getElementById('btn-resend');
    const label    = document.getElementById('btn-resend-label');
    const progress = document.getElementById('resend-progress');
    btn.disabled = true;
    label.innerHTML = 'Renvoyer dans <span id="resend-countdown">' + remaining + '</span>s';
    if (progress) {
        progress.style.transition = 'none';
        progress.style.width = '100%';
        // Force reflow so the next transition applies cleanly.
        // eslint-disable-next-line no-unused-expressions
        progress.offsetWidth;
        progress.style.transition = 'width ' + RESEND_COOLDOWN_S + 's linear';
        progress.style.width = '0%';
    }
    _resendTimer = setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
            clearInterval(_resendTimer); _resendTimer = null;
            unlockResend();
            return;
        }
        const live = document.getElementById('resend-countdown');
        if (live) live.textContent = remaining;
    }, 1000);
}
function unlockResend() {
    if (_resendTimer) { clearInterval(_resendTimer); _resendTimer = null; }
    const btn      = document.getElementById('btn-resend');
    const label    = document.getElementById('btn-resend-label');
    const progress = document.getElementById('resend-progress');
    btn.disabled = false;
    label.textContent = 'Renvoyer le code';
    if (progress) { progress.style.transition = 'none'; progress.style.width = '0%'; }
}

// ── Dynamic members ───────────────────────────────────────────────────────────
let dCount = 0;
function addDescendant(prefill) {
    const i   = dCount++;
    document.getElementById('descendants_count').value = dCount;
    const div = document.createElement('div');
    div.className = 'member-card'; div.id = 'descendant-' + i;
    const ciOld    = prefill?.ci    ?? '';
    const photoOld = prefill?.photo ?? '';
    const ciUrl    = prefill?._ci_url    ?? '';
    const photoUrl = prefill?._photo_url ?? '';
    div.innerHTML = `
        <div class="card-label">Descendant ${i + 1}</div>
        <button type="button" class="remove-btn" onclick="removeMember('descendant-${i}','descendants_count','descendants-container')">Supprimer</button>
        <input type="hidden" name="descendant_ci_old_${i}"    value="${escapeAttr(ciOld)}">
        <input type="hidden" name="descendant_photo_old_${i}" value="${escapeAttr(photoOld)}">
        <div class="grid-3">
            <div class="field span-3">
                <label>Nom complet</label>
                <input type="text" name="descendant_nom_${i}" placeholder="Prénom et Nom" value="${escapeAttr(prefill?.nom ?? '')}">
            </div>
            <div class="field">
                <label>Carte d'identité</label>
                <input type="file" name="descendant_ci_${i}" accept=".jpg,.jpeg,.pdf">
                ${ciUrl ? '<div class="file-existing"><a href="'+escapeAttr(ciUrl)+'" target="_blank">Fichier actuel</a></div>' : ''}
            </div>
            <div class="field">
                <label>Photo</label>
                <input type="file" name="descendant_photo_${i}" accept=".jpg,.jpeg,.pdf">
                ${photoUrl ? '<div class="file-existing"><a href="'+escapeAttr(photoUrl)+'" target="_blank">Fichier actuel</a></div>' : ''}
            </div>
        </div>`;
    document.getElementById('descendants-container').appendChild(div);
}

let fCount = 0;
function addFratrie(prefill) {
    const i   = fCount++;
    document.getElementById('fratrie_count').value = fCount;
    const div = document.createElement('div');
    div.className = 'member-card'; div.id = 'fratrie-' + i;
    const type     = prefill?._type ?? 'frere';
    const ciOld    = prefill?.ci    ?? '';
    const photoOld = prefill?.photo ?? '';
    const ciUrl    = prefill?._ci_url    ?? '';
    const photoUrl = prefill?._photo_url ?? '';
    div.innerHTML = `
        <div class="card-label">Membre ${i + 1}</div>
        <button type="button" class="remove-btn" onclick="removeMember('fratrie-${i}','fratrie_count','fratrie-container')">Supprimer</button>
        <input type="hidden" name="fratrie_ci_old_${i}"    value="${escapeAttr(ciOld)}">
        <input type="hidden" name="fratrie_photo_old_${i}" value="${escapeAttr(photoOld)}">
        <div class="grid-3">
            <div class="field">
                <label>Type</label>
                <select name="fratrie_type_${i}">
                    <option value="frere" ${type==='frere'?'selected':''}>Frère</option>
                    <option value="soeur" ${type==='soeur'?'selected':''}>Sœur</option>
                </select>
            </div>
            <div class="field span-2" style="grid-column:span 2">
                <label>Nom complet</label>
                <input type="text" name="fratrie_nom_${i}" placeholder="Prénom et Nom" value="${escapeAttr(prefill?.nom ?? '')}">
            </div>
            <div class="field">
                <label>Carte d'identité</label>
                <input type="file" name="fratrie_ci_${i}" accept=".jpg,.jpeg,.pdf">
                ${ciUrl ? '<div class="file-existing"><a href="'+escapeAttr(ciUrl)+'" target="_blank">Fichier actuel</a></div>' : ''}
            </div>
            <div class="field">
                <label>Photo</label>
                <input type="file" name="fratrie_photo_${i}" accept=".jpg,.jpeg,.pdf">
                ${photoUrl ? '<div class="file-existing"><a href="'+escapeAttr(photoUrl)+'" target="_blank">Fichier actuel</a></div>' : ''}
            </div>
        </div>`;
    document.getElementById('fratrie-container').appendChild(div);
}

function removeMember(id, counterId, containerId) {
    document.getElementById(id)?.remove();
    document.getElementById(counterId).value =
        document.getElementById(containerId).querySelectorAll('.member-card').length;
}

// ── Pre-fill dynamic sections from server data ────────────────────────────────
function prefillDynamic() {
    if (!window._prefill) return;
    (window._prefill.freres  || []).forEach(f  => addFratrie({...f, _type:'frere'}));
    (window._prefill.soeurs  || []).forEach(s  => addFratrie({...s, _type:'soeur'}));
    (window._prefill.descendants || []).forEach(d => addDescendant(d));
}

// ── Form submit ───────────────────────────────────────────────────────────────
function setSubmitIdle() {
    const btn = document.getElementById('submit-btn');
    btn.disabled    = false;
    btn.textContent = IS_UPDATE ? 'Mettre à jour ma fiche' : 'Soumettre ma fiche';
}
function setSubmitLoading() {
    const btn = document.getElementById('submit-btn');
    btn.disabled    = true;
    btn.textContent = IS_UPDATE ? 'Mise à jour…' : 'Envoi en cours…';
}

document.getElementById('cnass-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    setSubmitLoading();
    hideEl('alert-form-error');
    try {
        const fd   = new FormData(this);
        const res  = await fetch(SUBMIT_URL, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            // Reload so the page comes back with the freshly-saved data
            // and lands on the read-only view panel.
            window.location.href = '{{ route("form") }}?saved=1';
        } else {
            showErr('alert-form-error', data.message ?? 'Une erreur est survenue.');
            setSubmitIdle();
        }
    } catch {
        showErr('alert-form-error', 'Erreur réseau. Veuillez réessayer.');
        setSubmitIdle();
    }
});

function switchToForm() {
    hideEl('panel-view');
    showEl('panel-form');
    setSubmitIdle();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Helpers ───────────────────────────────────────────────────────────────────
async function postJson(url, body) {
    const r = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
    const text = await r.text();
    try {
        return JSON.parse(text);
    } catch {
        throw new Error('Serveur: HTTP ' + r.status + ' — ' + text.substring(0, 200));
    }
}
function showEl(id) { document.getElementById(id)?.classList.remove('hidden'); }
function hideEl(id) { document.getElementById(id)?.classList.add('hidden'); }
function showErr(id, msg) { const el = document.getElementById(id); el.textContent = msg; el.classList.remove('hidden'); }
function escapeAttr(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Init: if server already verified, pre-fill dynamic sections ───────────────
document.addEventListener('DOMContentLoaded', () => {
    prefillDynamic();
    toggleConjoint();

    // Show "saved" banner once after a successful save, then strip the
    // ?saved=1 from the URL so refreshing won't show it again.
    if (new URLSearchParams(window.location.search).get('saved') === '1') {
        const banner = document.getElementById('saved-banner');
        if (banner) {
            banner.classList.remove('hidden');
            setTimeout(() => banner.classList.add('hidden'), 4500);
        }
        history.replaceState({}, '', '{{ route("form") }}');
    }
});
</script>
</body>
</html>

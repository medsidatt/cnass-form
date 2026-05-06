<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche CNASS – Situation Familiale</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #eef1f6; color: #1e293b; min-height: 100vh; }

        .container { max-width: 880px; margin: 36px auto 64px; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }

        header { background: #1a3a6e; color: #fff; padding: 28px 40px; display: flex; align-items: center; gap: 16px; }
        header .logo { font-size: 2rem; }
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
        .otp-icon { font-size: 2.8rem; margin-bottom: 14px; }

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

        /* Alerts */
        .alert { padding: 12px 16px; border-radius: 7px; margin-bottom: 16px; font-size: .84rem; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }

        /* Download / success panel */
        .success-card { text-align: center; padding: 20px 0; }
        .success-card .icon { font-size: 3.5rem; margin-bottom: 14px; }
        .success-card h2 { font-size: 1.3rem; color: #16a34a; margin-bottom: 6px; }
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
        <div class="logo">🏢</div>
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
                <div class="otp-icon">📱</div>
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
                    <p style="font-size:.84rem;color:#64748b;margin-bottom:16px">
                        Code envoyé sur <strong id="phone-display"></strong>. Consultez votre WhatsApp.
                    </p>
                    <div class="field" style="margin-bottom:16px">
                        <label>Code de vérification (6 chiffres)</label>
                        <div class="input-row">
                            <input type="text" id="otp-input" class="otp-input" maxlength="6" placeholder="· · · · · ·" inputmode="numeric" autocomplete="one-time-code">
                            <button type="button" class="btn btn-primary" id="btn-verify" onclick="verifyOtp()">Vérifier</button>
                        </div>
                    </div>
                    <button type="button" class="btn-link" onclick="resetPhone()">← Changer de numéro</button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             PANEL 2 – Form
        ══════════════════════════════════════════ --}}
        <div id="panel-form" class="{{ !$phoneVerified ? 'hidden' : '' }}">

            <div class="steps">
                <div class="step done"><div class="num">✓</div>Vérification</div>
                <div class="step active"><div class="num">2</div>Formulaire</div>
                <div class="step"><div class="num">3</div>Confirmation</div>
            </div>

            {{-- Existing submission banner --}}
            @if($existing)
            <div class="existing-banner">
                <div class="info">
                    <strong>📋 Fiche existante — {{ $existing->nom_complet }}</strong>
                    <small>Dernière mise à jour : {{ $existing->updated_at->format('d/m/Y à H:i') }} · Vous pouvez modifier vos informations ci-dessous.</small>
                </div>
                <a href="{{ route('download', $existing->id) }}" class="dl-small">⬇ Télécharger</a>
            </div>
            @endif

            <div id="alert-form-error" class="alert alert-error hidden"></div>
            <div id="form-phone-badge" class="{{ $phoneVerified ? '' : 'hidden' }}" style="font-size:.78rem;color:#16a34a;margin-bottom:20px">
                ✅ Connecté : <strong>{{ $verifiedPhone }}</strong>
            </div>

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
                            <input type="file" name="ci_employe" accept=".pdf,.jpg,.jpeg,.png">
                            @if(!empty($existing->ci_employe))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->ci_employe) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_employe" accept=".jpg,.jpeg,.png">
                            @if(!empty($existing->photo_employe))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->photo_employe) }}" target="_blank">Fichier actuel</a></div>
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
                            <input type="file" name="ci_pere" accept=".pdf,.jpg,.jpeg,.png">
                            @if(!empty($existing->ci_pere))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->ci_pere) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_pere" accept=".jpg,.jpeg,.png">
                            @if(!empty($existing->photo_pere))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->photo_pere) }}" target="_blank">Fichier actuel</a></div>
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
                            <input type="file" name="ci_mere" accept=".pdf,.jpg,.jpeg,.png">
                            @if(!empty($existing->ci_mere))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->ci_mere) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_mere" accept=".jpg,.jpeg,.png">
                            @if(!empty($existing->photo_mere))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->photo_mere) }}" target="_blank">Fichier actuel</a></div>
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
                            <input type="file" name="ci_conjoint" accept=".pdf,.jpg,.jpeg,.png">
                            @if(!empty($existing->ci_conjoint))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->ci_conjoint) }}" target="_blank">Fichier actuel</a></div>
                            @endif
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_conjoint" accept=".jpg,.jpeg,.png">
                            @if(!empty($existing->photo_conjoint))
                                <div class="file-existing">📎 <a href="{{ Storage::url($existing->photo_conjoint) }}" target="_blank">Fichier actuel</a></div>
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
                    {{ $existing ? '💾 Mettre à jour ma fiche' : '✔ Soumettre ma fiche' }}
                </button>
            </form>
        </div>

        {{-- ══════════════════════════════════════════
             PANEL 3 – Confirmation / Download
        ══════════════════════════════════════════ --}}
        <div id="panel-download" class="hidden">
            <div class="steps">
                <div class="step done"><div class="num">✓</div>Vérification</div>
                <div class="step done"><div class="num">✓</div>Formulaire</div>
                <div class="step active"><div class="num">3</div>Confirmation</div>
            </div>
            <div class="success-card">
                <div class="icon">✅</div>
                <h2 id="success-title">Fiche enregistrée</h2>
                <p id="success-name"></p>
                <div class="success-actions">
                    <a id="dl-link" href="#" class="btn-dl">⬇ Télécharger ma fiche Excel</a>
                    <button type="button" class="btn-modify" onclick="showForm()">✏️ Modifier ma fiche</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Pre-fill data for JS --}}
@if($existing)
<script>
window._prefill = {
    freres:      @json($existing->freres      ?? []),
    soeurs:      @json($existing->soeurs      ?? []),
    descendants: @json($existing->descendants ?? []),
};
</script>
@endif

<script>
const SEND_URL   = '{{ route("verify.send") }}';
const CHECK_URL  = '{{ route("verify.check") }}';
const SUBMIT_URL = '{{ route("submit") }}';
const DL_BASE    = '{{ url("/download") }}';
const CSRF       = '{{ csrf_token() }}';
const IS_UPDATE  = {{ $existing ? 'true' : 'false' }};

// ── Situation familiale ──────────────────────────────────────────────────────
function toggleConjoint() {
    const val = document.getElementById('situation_familiale').value;
    document.getElementById('conjoint-section').style.display = val === 'marié(e)' ? 'block' : 'none';
}
document.getElementById('situation_familiale').addEventListener('change', toggleConjoint);

// ── WhatsApp OTP ─────────────────────────────────────────────────────────────
async function sendOtp() {
    let digits = document.getElementById('phone-input').value.trim().replace(/\s+/g, '');
    // Strip any leading + or 222 the user may have typed; the server prepends +222
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
        } else {
            showErr('err-phone', data.message);
        }
    } catch (e) { showErr('err-phone', e.message || 'Erreur réseau. Veuillez réessayer.'); }
    finally { btn.disabled = false; btn.textContent = 'Envoyer le code'; }
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
            window.location.href = '/';
        } else {
            showErr('err-otp', data.message);
        }
    } catch (e) { showErr('err-otp', e.message || 'Erreur réseau. Veuillez réessayer.'); }
    finally { btn.disabled = false; btn.textContent = 'Vérifier'; }
}

function resetPhone() {
    document.getElementById('otp-input').value = '';
    hideEl('err-otp'); showEl('phone-step'); hideEl('otp-step');
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
    div.innerHTML = `
        <div class="card-label">Descendant ${i + 1}</div>
        <button type="button" class="remove-btn" onclick="removeMember('descendant-${i}','descendants_count','descendants-container')">✕ Supprimer</button>
        <input type="hidden" name="descendant_ci_old_${i}"    value="${ciOld}">
        <input type="hidden" name="descendant_photo_old_${i}" value="${photoOld}">
        <div class="grid-3">
            <div class="field span-3">
                <label>Nom complet</label>
                <input type="text" name="descendant_nom_${i}" placeholder="Prénom et Nom" value="${prefill?.nom ?? ''}">
            </div>
            <div class="field">
                <label>Carte d'identité</label>
                <input type="file" name="descendant_ci_${i}" accept=".pdf,.jpg,.jpeg,.png">
                ${ciOld ? '<div class="file-existing">📎 <a href="/storage/'+ciOld+'" target="_blank">Fichier actuel</a></div>' : ''}
            </div>
            <div class="field">
                <label>Photo</label>
                <input type="file" name="descendant_photo_${i}" accept=".jpg,.jpeg,.png">
                ${photoOld ? '<div class="file-existing">📎 <a href="/storage/'+photoOld+'" target="_blank">Fichier actuel</a></div>' : ''}
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
    div.innerHTML = `
        <div class="card-label">Membre ${i + 1}</div>
        <button type="button" class="remove-btn" onclick="removeMember('fratrie-${i}','fratrie_count','fratrie-container')">✕ Supprimer</button>
        <input type="hidden" name="fratrie_ci_old_${i}"    value="${ciOld}">
        <input type="hidden" name="fratrie_photo_old_${i}" value="${photoOld}">
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
                <input type="text" name="fratrie_nom_${i}" placeholder="Prénom et Nom" value="${prefill?.nom ?? ''}">
            </div>
            <div class="field">
                <label>Carte d'identité</label>
                <input type="file" name="fratrie_ci_${i}" accept=".pdf,.jpg,.jpeg,.png">
                ${ciOld ? '<div class="file-existing">📎 <a href="/storage/'+ciOld+'" target="_blank">Fichier actuel</a></div>' : ''}
            </div>
            <div class="field">
                <label>Photo</label>
                <input type="file" name="fratrie_photo_${i}" accept=".jpg,.jpeg,.png">
                ${photoOld ? '<div class="file-existing">📎 <a href="/storage/'+photoOld+'" target="_blank">Fichier actuel</a></div>' : ''}
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
document.getElementById('cnass-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = IS_UPDATE ? 'Mise à jour…' : 'Envoi en cours…';
    hideEl('alert-form-error');
    try {
        const fd   = new FormData(this);
        const res  = await fetch(SUBMIT_URL, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            const name = fd.get('nom_complet');
            document.getElementById('success-title').textContent = data.updated ? 'Fiche mise à jour' : 'Fiche enregistrée';
            document.getElementById('success-name').textContent  = name;
            document.getElementById('dl-link').href = DL_BASE + '/' + data.submission_id;
            hideEl('panel-form');
            showEl('panel-download');
        } else {
            showErr('alert-form-error', data.message ?? 'Une erreur est survenue.');
            btn.disabled = false;
            btn.textContent = IS_UPDATE ? '💾 Mettre à jour ma fiche' : '✔ Soumettre ma fiche';
        }
    } catch {
        showErr('alert-form-error', 'Erreur réseau. Veuillez réessayer.');
        btn.disabled = false;
        btn.textContent = IS_UPDATE ? '💾 Mettre à jour ma fiche' : '✔ Soumettre ma fiche';
    }
});

function showForm() {
    hideEl('panel-download');
    showEl('panel-form');
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

// ── Init: if server already verified, pre-fill dynamic sections ───────────────
document.addEventListener('DOMContentLoaded', () => {
    prefillDynamic();
    toggleConjoint();
});
</script>
</body>
</html>

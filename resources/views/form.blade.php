<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche CNASS – Situation Familiale</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; min-height: 100vh; }

        .container { max-width: 860px; margin: 40px auto 60px; background: #fff; border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,.1); overflow: hidden; }
        header { background: #1a3a6e; color: #fff; padding: 24px 36px; }
        header h1 { font-size: 1.35rem; }
        header p  { font-size: .82rem; opacity: .75; margin-top: 5px; }
        .form-body { padding: 36px; }

        /* ── Steps ── */
        .step-indicator { display: flex; gap: 0; margin-bottom: 32px; }
        .step { flex: 1; text-align: center; padding: 10px 4px; font-size: .75rem; font-weight: 600; color: #94a3b8; border-bottom: 3px solid #e2e8f0; position: relative; }
        .step.active  { color: #1a3a6e; border-bottom-color: #1a3a6e; }
        .step.done    { color: #16a34a; border-bottom-color: #16a34a; }
        .step .num    { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #e2e8f0; color: #64748b; font-size: .75rem; margin-bottom: 4px; }
        .step.active .num { background: #1a3a6e; color: #fff; }
        .step.done .num   { background: #16a34a; color: #fff; }

        /* ── WA Verify panel ── */
        .wa-panel { max-width: 420px; margin: 0 auto; }
        .wa-panel h2 { font-size: 1.1rem; color: #1a3a6e; margin-bottom: 6px; }
        .wa-panel .sub { font-size: .84rem; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
        .wa-logo { font-size: 2.5rem; margin-bottom: 12px; }
        .phone-row { display: flex; gap: 10px; }
        .phone-row input { flex: 1; }
        .otp-row { display: flex; gap: 10px; }
        .otp-row input { flex: 1; letter-spacing: .3em; font-size: 1.1rem; text-align: center; }

        /* ── Sections ── */
        .section { margin-bottom: 30px; }
        .section-title { font-size: .95rem; font-weight: 700; color: #1a3a6e; border-bottom: 2px solid #1a3a6e; padding-bottom: 6px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .col-span-2 { grid-column: span 2; }
        .col-span-3 { grid-column: span 3; }

        /* ── Fields ── */
        .field { display: flex; flex-direction: column; gap: 5px; }
        label  { font-size: .8rem; font-weight: 600; color: #444; }
        input[type="text"], input[type="tel"],
        select, input[type="file"] {
            border: 1px solid #d0d5dd; border-radius: 6px; padding: 9px 12px;
            font-size: .88rem; width: 100%; background: #fff; transition: border-color .15s;
        }
        input:focus, select:focus { outline: none; border-color: #1a3a6e; }
        input[type="file"] { padding: 5px 10px; cursor: pointer; }

        /* ── Member cards ── */
        .member-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 12px; position: relative; background: #fafbff; }
        .member-card .card-label { font-size: .78rem; font-weight: 700; color: #1a3a6e; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 12px; }
        .remove-btn { position: absolute; top: 12px; right: 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 5px; padding: 3px 11px; cursor: pointer; font-size: .78rem; font-weight: 600; }
        .remove-btn:hover { background: #fca5a5; }
        .add-btn { background: #eef2ff; color: #1a3a6e; border: 1px solid #c7d2fe; border-radius: 6px; padding: 8px 18px; cursor: pointer; font-size: .82rem; font-weight: 600; margin-top: 6px; }
        .add-btn:hover { background: #c7d2fe; }

        /* ── Buttons ── */
        .btn-primary { display: inline-block; padding: 11px 24px; background: #1a3a6e; color: #fff; border: none; border-radius: 7px; font-size: .9rem; font-weight: 700; cursor: pointer; transition: background .15s; }
        .btn-primary:hover:not(:disabled) { background: #14316a; }
        .btn-primary:disabled { opacity: .6; cursor: not-allowed; }
        .btn-primary.full { width: 100%; padding: 14px; font-size: 1rem; }
        .btn-wa { background: #25d366; }
        .btn-wa:hover:not(:disabled) { background: #1ebe59; }
        .btn-link { background: none; border: none; color: #1a3a6e; font-size: .82rem; cursor: pointer; text-decoration: underline; padding: 0; margin-top: 10px; }

        /* ── Alerts ── */
        .alert { padding: 11px 15px; border-radius: 7px; margin-bottom: 16px; font-size: .85rem; }
        .alert-error   { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #86efac; }
        .hidden { display: none !important; }

        /* ── Download panel ── */
        .success-card { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 36px; text-align: center; }
        .success-card .check { font-size: 3rem; margin-bottom: 12px; }
        .success-card h2 { color: #16a34a; font-size: 1.25rem; margin-bottom: 6px; }
        .success-card p { color: #555; font-size: .9rem; margin-bottom: 24px; }
        .dl-btn  { display: inline-block; background: #1a3a6e; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-weight: 700; font-size: .95rem; margin: 6px; }
        .dl-btn:hover { background: #14316a; }
        .new-btn { display: inline-block; background: #f1f5f9; color: #475569; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-weight: 600; font-size: .95rem; margin: 6px; border: 1px solid #cbd5e1; }
        .new-btn:hover { background: #e2e8f0; }

        #conjoint-section { display: none; }

        @media (max-width: 640px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .col-span-2, .col-span-3 { grid-column: span 1; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Fiche de Situation Familiale – CNASS</h1>
        <p>Authentification WhatsApp requise avant de remplir le formulaire.</p>
    </header>

    <div class="form-body">

        {{-- ══════════════════════════════════════════════
             PANEL 3 – Download (submitted)
        ══════════════════════════════════════════════ --}}
        <div id="panel-download" class="{{ $submission ? '' : 'hidden' }}">
            <div class="success-card">
                <div class="check">✅</div>
                <h2>Fiche soumise avec succès !</h2>
                <p id="success-name">
                    @if($submission)
                        Fiche de <strong>{{ $submission->nom_complet }}</strong>
                        enregistrée le {{ $submission->created_at->format('d/m/Y à H:i') }}.
                    @endif
                </p>
                <div>
                    <a id="dl-link"
                       href="{{ $submission ? route('download', $submission->id) : '#' }}"
                       class="dl-btn">⬇ Télécharger ma fiche Excel</a>
                    <a href="{{ route('session.reset') }}" class="new-btn">+ Nouvelle soumission</a>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             PANEL 1 – WhatsApp OTP
        ══════════════════════════════════════════════ --}}
        <div id="panel-verify" class="{{ ($submission || $phoneVerified) ? 'hidden' : '' }}">

            <div class="step-indicator">
                <div class="step active" id="step1"><div class="num">1</div>Vérification</div>
                <div class="step"        id="step2"><div class="num">2</div>Formulaire</div>
                <div class="step"        id="step3"><div class="num">3</div>Confirmation</div>
            </div>

            <div class="wa-panel">
                <div class="wa-logo">💬</div>
                <h2>Vérification via WhatsApp</h2>
                <p class="sub">Un code à 6 chiffres sera envoyé sur votre numéro WhatsApp pour confirmer votre identité.</p>

                {{-- Phone entry --}}
                <div id="phone-step">
                    <div id="err-phone" class="alert alert-error hidden"></div>
                    <div class="field" style="margin-bottom:14px">
                        <label>Numéro WhatsApp (format international)</label>
                        <div class="phone-row">
                            <input type="tel" id="phone-input" placeholder="+213 6XX XXX XXX" autocomplete="tel">
                            <button type="button" class="btn-primary btn-wa" id="btn-send" onclick="sendOtp()">Envoyer</button>
                        </div>
                    </div>
                </div>

                {{-- OTP entry --}}
                <div id="otp-step" class="hidden">
                    <div id="err-otp" class="alert alert-error hidden"></div>
                    <p class="sub" style="margin-bottom:14px">Code envoyé sur <strong id="phone-display"></strong>. Vérifiez votre WhatsApp.</p>
                    <div class="field" style="margin-bottom:14px">
                        <label>Code à 6 chiffres</label>
                        <div class="otp-row">
                            <input type="text" id="otp-input" maxlength="6" placeholder="_ _ _ _ _ _" inputmode="numeric">
                            <button type="button" class="btn-primary" id="btn-verify" onclick="verifyOtp()">Vérifier</button>
                        </div>
                    </div>
                    <button type="button" class="btn-link" onclick="resetPhone()">← Changer de numéro</button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             PANEL 2 – Main form
        ══════════════════════════════════════════════ --}}
        <div id="panel-form" class="{{ ($submission || !$phoneVerified) ? 'hidden' : '' }}">

            <div class="step-indicator">
                <div class="step done"   id="fs1"><div class="num">✓</div>Vérification</div>
                <div class="step active" id="fs2"><div class="num">2</div>Formulaire</div>
                <div class="step"        id="fs3"><div class="num">3</div>Confirmation</div>
            </div>

            @if($phoneVerified)
                <p style="font-size:.82rem;color:#16a34a;margin-bottom:20px">
                    ✅ Connecté avec <strong>{{ $verifiedPhone }}</strong>
                </p>
            @endif
            <div id="form-phone-badge" class="hidden" style="font-size:.82rem;color:#16a34a;margin-bottom:20px"></div>

            <div id="alert-form-error" class="alert alert-error hidden"></div>

            <form id="cnass-form" enctype="multipart/form-data">
                @csrf

                {{-- EMPLOYÉ --}}
                <div class="section">
                    <div class="section-title">👤 Informations de l'employé</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet *</label>
                            <input type="text" name="nom_complet" required placeholder="Prénom et Nom">
                        </div>
                        <div class="field">
                            <label>Situation familiale *</label>
                            <select name="situation_familiale" id="situation_familiale" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="célibataire">Célibataire</option>
                                <option value="marié(e)">Marié(e)</option>
                                <option value="divorcé(e)">Divorcé(e)</option>
                                <option value="veuf/veuve">Veuf / Veuve</option>
                            </select>
                        </div>
                        <div class="field"></div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_employe" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_employe" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                {{-- PÈRE --}}
                <div class="section">
                    <div class="section-title">👨 Père</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet</label>
                            <input type="text" name="nom_pere" placeholder="Prénom et Nom">
                        </div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_pere" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_pere" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                {{-- MÈRE --}}
                <div class="section">
                    <div class="section-title">👩 Mère</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet</label>
                            <input type="text" name="nom_mere" placeholder="Prénom et Nom">
                        </div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_mere" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_mere" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                {{-- CONJOINT(E) --}}
                <div class="section" id="conjoint-section">
                    <div class="section-title">💑 Conjoint(e)</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet</label>
                            <input type="text" name="nom_conjoint" placeholder="Prénom et Nom">
                        </div>
                        <div class="field">
                            <label>Carte d'identité</label>
                            <input type="file" name="ci_conjoint" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="field">
                            <label>Photo</label>
                            <input type="file" name="photo_conjoint" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                {{-- DESCENDANTS --}}
                <div class="section">
                    <div class="section-title">👶 Descendants</div>
                    <input type="hidden" name="descendants_count" id="descendants_count" value="0">
                    <div id="descendants-container"></div>
                    <button type="button" class="add-btn" onclick="addDescendant()">+ Ajouter un descendant</button>
                </div>

                {{-- FRATRIE --}}
                <div class="section">
                    <div class="section-title">👫 Fratrie (Frères &amp; Sœurs)</div>
                    <input type="hidden" name="fratrie_count" id="fratrie_count" value="0">
                    <div id="fratrie-container"></div>
                    <button type="button" class="add-btn" onclick="addFratrie()">+ Ajouter un(e) frère / sœur</button>
                </div>

                <button type="submit" class="btn-primary full" id="submit-btn">Soumettre la fiche</button>
            </form>
        </div>

    </div>{{-- /form-body --}}
</div>

<script>
const SEND_URL   = '{{ route("verify.send") }}';
const CHECK_URL  = '{{ route("verify.check") }}';
const SUBMIT_URL = '{{ route("submit") }}';
const DL_BASE    = '{{ url("/download") }}';
const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content
                ?? '{{ csrf_token() }}';

// ── WhatsApp OTP ────────────────────────────────────────────────────────────

async function sendOtp() {
    const phone = document.getElementById('phone-input').value.trim();
    if (!phone) return;
    const btn = document.getElementById('btn-send');
    btn.disabled = true; btn.textContent = 'Envoi…';
    hideErr('err-phone');

    try {
        const res  = await post(SEND_URL, { phone });
        const data = await res.json();
        if (data.success) {
            document.getElementById('phone-display').textContent = phone;
            document.getElementById('phone-step').classList.add('hidden');
            document.getElementById('otp-step').classList.remove('hidden');
        } else {
            showErr('err-phone', data.message);
        }
    } catch { showErr('err-phone', 'Erreur réseau.'); }
    finally { btn.disabled = false; btn.textContent = 'Envoyer'; }
}

async function verifyOtp() {
    const code = document.getElementById('otp-input').value.trim();
    if (code.length < 6) return;
    const btn = document.getElementById('btn-verify');
    btn.disabled = true; btn.textContent = 'Vérification…';
    hideErr('err-otp');

    try {
        const res  = await post(CHECK_URL, { code });
        const data = await res.json();
        if (data.success) {
            const phone = document.getElementById('phone-display').textContent;
            goToForm(phone);
        } else {
            showErr('err-otp', data.message);
        }
    } catch { showErr('err-otp', 'Erreur réseau.'); }
    finally { btn.disabled = false; btn.textContent = 'Vérifier'; }
}

function resetPhone() {
    document.getElementById('otp-step').classList.add('hidden');
    document.getElementById('otp-input').value = '';
    document.getElementById('phone-step').classList.remove('hidden');
    hideErr('err-otp');
}

function goToForm(phone) {
    document.getElementById('panel-verify').classList.add('hidden');
    const badge = document.getElementById('form-phone-badge');
    badge.innerHTML = '✅ Connecté avec <strong>' + phone + '</strong>';
    badge.classList.remove('hidden');
    document.getElementById('panel-form').classList.remove('hidden');
}

// ── Situation familiale → conjoint ──────────────────────────────────────────

document.getElementById('situation_familiale').addEventListener('change', function () {
    document.getElementById('conjoint-section').style.display =
        this.value === 'marié(e)' ? 'block' : 'none';
});

// ── Dynamic members ─────────────────────────────────────────────────────────

let dCount = 0;
function addDescendant() {
    const i = dCount++;
    document.getElementById('descendants_count').value = dCount;
    const div = document.createElement('div');
    div.className = 'member-card'; div.id = 'descendant-' + i;
    div.innerHTML = `
        <div class="card-label">Descendant ${i + 1}</div>
        <button type="button" class="remove-btn" onclick="removeMember('descendant-${i}','descendants_count','descendants-container')">✕</button>
        <div class="grid-3">
            <div class="field col-span-3">
                <label>Nom complet</label>
                <input type="text" name="descendant_nom_${i}" placeholder="Prénom et Nom">
            </div>
            <div class="field"><label>Carte d'identité</label><input type="file" name="descendant_ci_${i}" accept=".pdf,.jpg,.jpeg,.png"></div>
            <div class="field"><label>Photo</label><input type="file" name="descendant_photo_${i}" accept=".jpg,.jpeg,.png"></div>
        </div>`;
    document.getElementById('descendants-container').appendChild(div);
}

let fCount = 0;
function addFratrie() {
    const i = fCount++;
    document.getElementById('fratrie_count').value = fCount;
    const div = document.createElement('div');
    div.className = 'member-card'; div.id = 'fratrie-' + i;
    div.innerHTML = `
        <div class="card-label">Membre ${i + 1}</div>
        <button type="button" class="remove-btn" onclick="removeMember('fratrie-${i}','fratrie_count','fratrie-container')">✕</button>
        <div class="grid-3">
            <div class="field">
                <label>Type</label>
                <select name="fratrie_type_${i}"><option value="frere">Frère</option><option value="soeur">Sœur</option></select>
            </div>
            <div class="field col-span-2" style="grid-column:span 2">
                <label>Nom complet</label>
                <input type="text" name="fratrie_nom_${i}" placeholder="Prénom et Nom">
            </div>
            <div class="field"><label>Carte d'identité</label><input type="file" name="fratrie_ci_${i}" accept=".pdf,.jpg,.jpeg,.png"></div>
            <div class="field"><label>Photo</label><input type="file" name="fratrie_photo_${i}" accept=".jpg,.jpeg,.png"></div>
        </div>`;
    document.getElementById('fratrie-container').appendChild(div);
}

function removeMember(id, counterId, containerId) {
    document.getElementById(id)?.remove();
    document.getElementById(counterId).value =
        document.getElementById(containerId).querySelectorAll('.member-card').length;
}

// ── Form submit ─────────────────────────────────────────────────────────────

document.getElementById('cnass-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    btn.disabled = true; btn.textContent = 'Envoi en cours…';
    document.getElementById('alert-form-error').classList.add('hidden');

    try {
        const fd   = new FormData(this);
        const res  = await fetch(SUBMIT_URL, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            const name = fd.get('nom_complet');
            document.getElementById('success-name').innerHTML =
                'Fiche de <strong>' + name + '</strong> enregistrée.';
            document.getElementById('dl-link').href = DL_BASE + '/' + data.submission_id;
            document.getElementById('panel-form').classList.add('hidden');
            document.getElementById('panel-download').classList.remove('hidden');
        } else {
            showErr('alert-form-error', data.message ?? 'Une erreur est survenue.');
            btn.disabled = false; btn.textContent = 'Soumettre la fiche';
        }
    } catch {
        showErr('alert-form-error', 'Erreur réseau. Veuillez réessayer.');
        btn.disabled = false; btn.textContent = 'Soumettre la fiche';
    }
});

// ── Helpers ─────────────────────────────────────────────────────────────────

function post(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
}
function showErr(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg; el.classList.remove('hidden');
}
function hideErr(id) { document.getElementById(id).classList.add('hidden'); }
</script>
</body>
</html>

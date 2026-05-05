<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche CNASS – Situation Familiale</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; min-height: 100vh; }

        /* ── Layout ── */
        .container { max-width: 860px; margin: 40px auto 60px; background: #fff; border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,.1); overflow: hidden; }
        header { background: #1a3a6e; color: #fff; padding: 24px 36px; }
        header h1 { font-size: 1.35rem; }
        header p  { font-size: .82rem; opacity: .75; margin-top: 5px; }
        .form-body { padding: 36px; }

        /* ── Sections ── */
        .section { margin-bottom: 30px; }
        .section-title { font-size: .95rem; font-weight: 700; color: #1a3a6e; border-bottom: 2px solid #1a3a6e; padding-bottom: 6px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .section-title .icon { font-size: 1rem; }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .col-span-2 { grid-column: span 2; }

        /* ── Fields ── */
        .field { display: flex; flex-direction: column; gap: 5px; }
        label { font-size: .8rem; font-weight: 600; color: #444; }
        input[type="text"],
        select,
        input[type="file"] {
            border: 1px solid #d0d5dd; border-radius: 6px; padding: 9px 12px;
            font-size: .88rem; width: 100%; background: #fff; transition: border-color .15s;
        }
        input[type="text"]:focus, select:focus { outline: none; border-color: #1a3a6e; }
        input[type="file"] { padding: 5px 10px; cursor: pointer; }

        /* ── Dynamic members ── */
        .member-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 12px; position: relative; background: #fafbff; }
        .member-card .card-label { font-size: .78rem; font-weight: 700; color: #1a3a6e; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 12px; }
        .remove-btn { position: absolute; top: 12px; right: 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 5px; padding: 3px 11px; cursor: pointer; font-size: .78rem; font-weight: 600; }
        .remove-btn:hover { background: #fca5a5; }
        .add-btn { background: #eef2ff; color: #1a3a6e; border: 1px solid #c7d2fe; border-radius: 6px; padding: 8px 18px; cursor: pointer; font-size: .82rem; font-weight: 600; margin-top: 6px; transition: background .15s; }
        .add-btn:hover { background: #c7d2fe; }

        /* ── Buttons ── */
        .submit-btn { display: block; width: 100%; padding: 14px; background: #1a3a6e; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; margin-top: 10px; transition: background .15s; }
        .submit-btn:hover:not(:disabled) { background: #14316a; }
        .submit-btn:disabled { opacity: .6; cursor: not-allowed; }

        /* ── Alerts ── */
        .alert { padding: 12px 16px; border-radius: 7px; margin-bottom: 20px; font-size: .88rem; display: none; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }

        /* ── Download panel ── */
        #download-panel { display: none; }
        #download-panel.visible { display: block; }
        .success-card { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 36px; text-align: center; }
        .success-card .check { font-size: 3rem; margin-bottom: 12px; }
        .success-card h2 { color: #16a34a; font-size: 1.25rem; margin-bottom: 6px; }
        .success-card p { color: #555; font-size: .9rem; margin-bottom: 24px; }
        .dl-btn { display: inline-block; background: #1a3a6e; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-weight: 700; font-size: .95rem; margin: 6px; transition: background .15s; }
        .dl-btn:hover { background: #14316a; }
        .new-btn { display: inline-block; background: #f1f5f9; color: #475569; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-weight: 600; font-size: .95rem; margin: 6px; border: 1px solid #cbd5e1; transition: background .15s; }
        .new-btn:hover { background: #e2e8f0; }

        /* ── Spouse hidden by default ── */
        #conjoint-section { display: none; }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .col-span-2 { grid-column: span 1; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Fiche de Situation Familiale – CNASS</h1>
        <p>Remplissez le formulaire ci-dessous. Documents acceptés : PDF, JPG, PNG.</p>
    </header>

    <div class="form-body">

        {{-- ── Download panel (shown after submission or if session exists) ── --}}
        <div id="download-panel" class="{{ $submission ? 'visible' : '' }}">
            <div class="success-card">
                <div class="check">✅</div>
                <h2>Fiche soumise avec succès !</h2>
                <p id="success-name">
                    @if($submission)
                        Fiche de <strong>{{ $submission->nom_complet }}</strong> enregistrée le {{ $submission->created_at->format('d/m/Y à H:i') }}.
                    @endif
                </p>
                <div>
                    <a id="dl-link"
                       href="{{ $submission ? route('download', $submission->id) : '#' }}"
                       class="dl-btn">
                        ⬇ Télécharger ma fiche Excel
                    </a>
                    <a href="{{ route('session.reset') }}" class="new-btn">
                        + Nouvelle soumission
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Main form (hidden if session exists) ── --}}
        <div id="form-wrapper" style="{{ $submission ? 'display:none' : '' }}">
            <div id="alert-error" class="alert alert-error"></div>

            <form id="cnass-form" enctype="multipart/form-data">
                @csrf

                {{-- ── EMPLOYÉ ── --}}
                <div class="section">
                    <div class="section-title"><span class="icon">👤</span> Informations de l'employé</div>
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
                            <label>Carte d'identité (employé)</label>
                            <input type="file" name="ci_employe" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="field">
                            <label>Photo (employé)</label>
                            <input type="file" name="photo_employe" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                {{-- ── PÈRE ── --}}
                <div class="section">
                    <div class="section-title"><span class="icon">👨</span> Père</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet du père</label>
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

                {{-- ── MÈRE ── --}}
                <div class="section">
                    <div class="section-title"><span class="icon">👩</span> Mère</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet de la mère</label>
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

                {{-- ── CONJOINT(E) (visible only when married) ── --}}
                <div class="section" id="conjoint-section">
                    <div class="section-title"><span class="icon">💑</span> Conjoint(e)</div>
                    <div class="grid-2">
                        <div class="field col-span-2">
                            <label>Nom complet du/de la conjoint(e)</label>
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

                {{-- ── DESCENDANTS ── --}}
                <div class="section">
                    <div class="section-title"><span class="icon">👶</span> Descendants (enfants)</div>
                    <input type="hidden" name="descendants_count" id="descendants_count" value="0">
                    <div id="descendants-container"></div>
                    <button type="button" class="add-btn" onclick="addDescendant()">+ Ajouter un descendant</button>
                </div>

                {{-- ── FRATRIE (Frères + Sœurs combined) ── --}}
                <div class="section">
                    <div class="section-title"><span class="icon">👫</span> Fratrie (Frères &amp; Sœurs)</div>
                    <input type="hidden" name="fratrie_count" id="fratrie_count" value="0">
                    <div id="fratrie-container"></div>
                    <button type="button" class="add-btn" onclick="addFratrie()">+ Ajouter un(e) frère / sœur</button>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">Soumettre la fiche</button>
            </form>
        </div>

    </div>
</div>

<script>
    // ── Situation familiale → show/hide conjoint ──────────────────────────────
    document.getElementById('situation_familiale').addEventListener('change', function () {
        const show = this.value === 'marié(e)';
        document.getElementById('conjoint-section').style.display = show ? 'block' : 'none';
    });

    // ── Descendants ──────────────────────────────────────────────────────────
    let dCount = 0;
    function addDescendant() {
        const i   = dCount++;
        document.getElementById('descendants_count').value = dCount;
        const div = document.createElement('div');
        div.className = 'member-card';
        div.id = 'descendant-' + i;
        div.innerHTML = `
            <div class="card-label">Descendant ${i + 1}</div>
            <button type="button" class="remove-btn" onclick="removeMember('descendant-${i}', 'descendants_count')">✕ Supprimer</button>
            <div class="grid-3">
                <div class="field col-span-2" style="grid-column:span 3">
                    <label>Nom complet</label>
                    <input type="text" name="descendant_nom_${i}" placeholder="Prénom et Nom">
                </div>
                <div class="field">
                    <label>Carte d'identité</label>
                    <input type="file" name="descendant_ci_${i}" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="field">
                    <label>Photo</label>
                    <input type="file" name="descendant_photo_${i}" accept=".jpg,.jpeg,.png">
                </div>
            </div>`;
        document.getElementById('descendants-container').appendChild(div);
    }

    // ── Fratrie ───────────────────────────────────────────────────────────────
    let fCount = 0;
    function addFratrie() {
        const i   = fCount++;
        document.getElementById('fratrie_count').value = fCount;
        const div = document.createElement('div');
        div.className = 'member-card';
        div.id = 'fratrie-' + i;
        div.innerHTML = `
            <div class="card-label">Membre de la fratrie ${i + 1}</div>
            <button type="button" class="remove-btn" onclick="removeMember('fratrie-${i}', 'fratrie_count')">✕ Supprimer</button>
            <div class="grid-3">
                <div class="field">
                    <label>Type</label>
                    <select name="fratrie_type_${i}">
                        <option value="frere">Frère</option>
                        <option value="soeur">Sœur</option>
                    </select>
                </div>
                <div class="field col-span-2">
                    <label>Nom complet</label>
                    <input type="text" name="fratrie_nom_${i}" placeholder="Prénom et Nom">
                </div>
                <div class="field">
                    <label>Carte d'identité</label>
                    <input type="file" name="fratrie_ci_${i}" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="field">
                    <label>Photo</label>
                    <input type="file" name="fratrie_photo_${i}" accept=".jpg,.jpeg,.png">
                </div>
            </div>`;
        document.getElementById('fratrie-container').appendChild(div);
    }

    // ── Remove member card, recount visible cards ─────────────────────────────
    function removeMember(id, counterId) {
        const el = document.getElementById(id);
        if (el) el.remove();
        const container = counterId === 'descendants_count'
            ? document.getElementById('descendants-container')
            : document.getElementById('fratrie-container');
        document.getElementById(counterId).value =
            container.querySelectorAll('.member-card').length;
    }

    // ── Form submit ───────────────────────────────────────────────────────────
    document.getElementById('cnass-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn = document.getElementById('submit-btn');
        btn.disabled    = true;
        btn.textContent = 'Envoi en cours…';
        document.getElementById('alert-error').style.display = 'none';

        try {
            const res  = await fetch('{{ route("submit") }}', {
                method: 'POST',
                body:   new FormData(this),
            });
            const data = await res.json();

            if (data.success) {
                // Switch to download panel
                document.getElementById('form-wrapper').style.display = 'none';

                const panel = document.getElementById('download-panel');
                document.getElementById('success-name').innerHTML =
                    'Fiche de <strong>' + (new FormData(this)).get('nom_complet') + '</strong> enregistrée.';
                document.getElementById('dl-link').href =
                    '{{ url("/download") }}/' + data.submission_id;

                panel.classList.add('visible');
            } else {
                showError(data.message ?? 'Une erreur est survenue.');
                btn.disabled    = false;
                btn.textContent = 'Soumettre la fiche';
            }
        } catch (err) {
            showError('Erreur réseau. Veuillez réessayer.');
            btn.disabled    = false;
            btn.textContent = 'Soumettre la fiche';
        }
    });

    function showError(msg) {
        const el = document.getElementById('alert-error');
        el.textContent    = msg;
        el.style.display  = 'block';
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>
</body>
</html>

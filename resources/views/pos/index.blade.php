@extends('layouts.app')

@section('content')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
    .pos-wrap { display: grid; grid-template-columns: 1fr 380px; gap: 16px; }
    @media (max-width: 767px) { .pos-wrap { grid-template-columns: 1fr; } }
    .scanner-input { font-size: 1rem; border: 2px solid #3b82f6; border-radius: 10px; }
    .scanner-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
    #recherche-resultats {
        position: absolute; z-index: 999; background: white;
        border: 1px solid #ddd; border-radius: 8px; width: 100%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12); max-height: 240px; overflow-y: auto;
    }
    #recherche-resultats .result-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f1f5f9; }
    #recherche-resultats .result-item:last-child { border-bottom: none; }
    #recherche-resultats .result-item:hover, #recherche-resultats .result-item:active { background: #f0f9ff; }
    .total-zone { background: linear-gradient(135deg, #0f172a, #1e3a5f); color: white; border-radius: 12px; }
    .btn-encaisser { background: linear-gradient(135deg, #16a34a, #15803d); border: none; }
    .monnaie-badge { background: #fef9c3; color: #854d0e; font-size: 1rem; font-weight: bold; border-radius: 8px; padding: 6px 12px; text-align: center; }
    #panier-liste { overflow-y: auto; max-height: 300px; }
    .btn-camera { background: #1e293b; color: white; border: none; border-radius: 8px; padding: 8px 14px; font-size: 0.85rem; cursor: pointer; transition: background .2s; }
    .btn-camera.actif { background: #dc2626; }
    #camera-zone { display:none; border-radius:12px; background:#0f172a; padding:10px; }
    #camera-reader { border-radius:10px; overflow:hidden; }
    #camera-reader video { border-radius:10px; }
    /* Masquer les éléments d'UI par défaut de html5-qrcode */
    #camera-reader__dashboard { display:none !important; }
    #camera-reader__scan_region { border: 3px solid #3b82f6 !important; border-radius:10px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">🖥️ Point de Vente</h2>
    <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary btn-sm">📋 Historique</a>
</div>

<div class="pos-wrap">

    {{-- ══ COLONNE GAUCHE : recherche ══ --}}
    <div class="d-flex flex-column gap-3">

        <div class="card border-0 shadow-sm">
            <div class="card-body pb-2">
                <div class="d-flex gap-2 mb-2">
                    <div class="position-relative flex-grow-1">
                        <input type="text" id="scanner-input" class="form-control scanner-input"
                               placeholder="🔍 Code-barres ou nom produit..."
                               autocomplete="off" autofocus>
                        <div id="recherche-resultats" style="display:none;"></div>
                    </div>
                    <button class="btn-camera" id="btn-camera" onclick="toggleCamera()">
                        📷 Caméra
                    </button>
                    <input type="file" id="camera-file" accept="image/*" capture="environment"
                           style="display:none;" onchange="scanFichierImage(this)">
                </div>
                <div class="text-muted small">💡 Tapez le nom, scannez un code USB, ou utilisez la caméra</div>
            </div>
        </div>

        <div id="camera-zone">
            <div id="camera-reader"></div>
            <button onclick="fermerCamera()"
                    class="btn btn-outline-danger btn-sm w-100 mt-2 fw-semibold">
                ✖ Fermer la caméra
            </button>
            <div id="scan-result-msg" class="mt-2 small fw-semibold text-center" style="display:none;"></div>
        </div>

    </div>

    {{-- ══ COLONNE DROITE : panier (UNE SEULE INSTANCE) ══ --}}
    {{-- CORRIGÉ : le panier n'est plus dupliqué mobile/desktop
         On utilise un seul bloc et CSS pour le repositionner --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">🛒 Panier</h5>
                <button class="btn btn-outline-danger btn-sm" onclick="viderPanier()">🗑️ Vider</button>
            </div>

            {{-- UNE SEULE liste panier --}}
            <div id="panier-liste" style="overflow-y:auto; max-height:280px; flex:1;">
                <div class="text-center text-muted py-4" id="panier-vide">
                    <div style="font-size:2.5rem">🛒</div>
                    <div>Panier vide</div>
                </div>
            </div>

            <div class="mt-3">
                <div class="total-zone p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>TOTAL</span>
                        <span class="fs-4 fw-bold" id="total-affiche">0 FCFA</span>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Montant reçu</label>
                        <input type="number" id="montant-recu" class="form-control form-control-sm"
                               placeholder="0" min="0" oninput="calculerMonnaie()">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Monnaie</label>
                        <div class="monnaie-badge" id="monnaie-rendue">— FCFA</div>
                    </div>
                </div>

                <select id="client-select" class="form-select form-select-sm mb-2"
                        onchange="afficherInfoCredit()">
                    <option value="">👤 Client comptoir</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}"
                                data-credit="{{ $c->a_credit ? 1 : 0 }}"
                                data-solde="{{ $c->solde }}"
                                data-bloque="{{ $c->solde >= 10000000 ? 1 : 0 }}">
                            {{ $c->nom }} — dette : {{ number_format($c->solde,0,',',' ') }} F
                        </option>
                    @endforeach
                </select>

                {{-- Jauge de crédit --}}
                <div id="credit-info" style="display:none;" class="mb-2 p-2 rounded" style="background:#f8fafc;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold" id="credit-label">Crédit utilisé</small>
                        <small id="credit-chiffres" class="fw-bold"></small>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div id="credit-bar" class="progress-bar" style="width:0%;"></div>
                    </div>
                    <div id="credit-restant" class="small mt-1 text-center fw-semibold"></div>
                </div>

                <div id="credit-option" style="display:none;" class="mb-2">
                    <div class="form-check">
                        <input type="checkbox" id="est-credit" class="form-check-input">
                        <label class="form-check-label text-danger small fw-semibold">
                            Vente à crédit
                        </label>
                    </div>
                </div>

                <button class="btn btn-encaisser btn-success w-100 py-2 text-white fw-bold"
                        onclick="encaisser()">💰 Encaisser</button>
            </div>
        </div>
    </div>

</div>

{{-- Modal succès --}}
<div class="modal fade" id="modalSucces" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div style="font-size:3rem;">✅</div>
                <h4 class="fw-bold mt-2">Vente enregistrée !</h4>
                <p class="text-muted mb-1" id="modal-numero"></p>
                <p class="fs-5 fw-bold text-success" id="modal-total"></p>
                <div class="alert alert-warning fw-bold my-3" id="modal-monnaie" style="display:none;"></div>
                <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                    <a id="btn-facture" href="#" target="_blank" class="btn btn-danger">🧾 Facture PDF</a>
                    <button class="btn btn-success" onclick="nouvelleVente()">➕ Nouvelle vente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ROUTES = {
    rechercher: '{{ route('pos.rechercher') }}',
    scanner:    '{{ route('pos.scanner') }}',
    vente:      '{{ route('pos.vente') }}',
    decoder:    '{{ route('pos.decoder') }}',
};

let panier = [];
let searchTimeout = null;

// ── Recherche texte ───────────────────────────
const scannerInput = document.getElementById('scanner-input');

scannerInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(searchTimeout);
        cacherResultats();
        const v = scannerInput.value.trim();
        if (v) scannerCodeBarre(v);
    }
});

scannerInput.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    const v = scannerInput.value.trim();
    if (v.length < 2) { cacherResultats(); return; }
    searchTimeout = setTimeout(() => rechercherProduit(v), 280);
});

async function rechercherProduit(q) {
    try {
        const res  = await fetch(ROUTES.rechercher + '?q=' + encodeURIComponent(q));
        const data = await res.json();
        afficherResultats(data);
    } catch(e) { cacherResultats(); }
}

function afficherResultats(produits) {
    const box = document.getElementById('recherche-resultats');
    if (!produits.length) { cacherResultats(); return; }

    box.innerHTML = '';
    produits.forEach(p => {
        const div = document.createElement('div');
        div.className = 'result-item';
        div.innerHTML =
            '<div class="fw-semibold">' + esc(p.nom) + '</div>' +
            '<div class="d-flex justify-content-between">' +
            '<small class="text-muted">' + (p.code_barre || 'Sans code') + '</small>' +
            '<small><strong>' + fmt(p.prix) + ' FCFA</strong> · Stock: ' + p.quantite + '</small></div>';

        // CORRIGÉ : onmousedown pour éviter que blur ferme le menu avant le clic
        div.addEventListener('mousedown', (e) => {
            e.preventDefault();
            ajouterAuPanier(p.id, p.nom, parseFloat(p.prix), p.quantite);
        });
        div.addEventListener('touchstart', (e) => {
            e.preventDefault();
            ajouterAuPanier(p.id, p.nom, parseFloat(p.prix), p.quantite);
        }, { passive: false });

        box.appendChild(div);
    });
    box.style.display = 'block';
}

function cacherResultats() {
    document.getElementById('recherche-resultats').style.display = 'none';
}

document.addEventListener('click', e => {
    if (!e.target.closest('#scanner-input') && !e.target.closest('#recherche-resultats'))
        cacherResultats();
});

// ── Scan code-barres USB ──────────────────────
async function scannerCodeBarre(code) {
    scannerInput.value = '';
    try {
        const res = await fetch(ROUTES.scanner, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({ code_barre: code })
        });
        if (res.ok) {
            const p = await res.json();
            ajouterAuPanier(p.id, p.nom, parseFloat(p.prix), p.quantite);
        } else {
            const data = await res.json();
            erreur(data.error || 'Produit introuvable');
        }
    } catch(e) { erreur('Erreur réseau — vérifiez la connexion'); }
    scannerInput.focus();
}

// ── Caméra : live (HTTPS) ou photo (HTTP) ────
let qrScanner    = null;
let scannerActif = false;
let dernierCode  = null;

function httpsOuLocal() {
    const h = location.hostname;
    return location.protocol === 'https:' ||
           h === 'localhost' || h === '127.0.0.1';
}

async function toggleCamera() {
    // HTTP sur IP locale (ex: 172.x.x.x) → Safari bloque getUserMedia
    // On utilise la capture photo native à la place
    if (!httpsOuLocal()) {
        document.getElementById('camera-file').click();
        return;
    }
    if (scannerActif) { await fermerCamera(); return; }
    await ouvrirCamera();
}

// ── Fallback photo (HTTP / Safari iOS sans HTTPS) ─
async function scanFichierImage(input) {
    if (!input.files || !input.files[0]) return;
    const btn = document.getElementById('btn-camera');
    btn.disabled    = true;
    btn.textContent = '⏳';

    try {
        // Div temporaire requis par html5-qrcode
        const tmpId  = 'qr-tmp-' + Date.now();
        const tmpDiv = document.createElement('div');
        tmpDiv.id    = tmpId;
        tmpDiv.style.display = 'none';
        document.body.appendChild(tmpDiv);

        const scanner    = new Html5Qrcode(tmpId);
        const decodedText = await scanner.scanFile(input.files[0], false);
        tmpDiv.remove();

        await scannerCodeBarre(decodedText);
    } catch (e) {
        erreur('Code non détecté — éclairage insuffisant ou image floue, réessayez.');
    } finally {
        btn.disabled    = false;
        btn.textContent = '📷 Caméra';
        input.value     = '';
    }
}

// ── Caméra live (HTTPS uniquement) ────────────
async function ouvrirCamera() {
    const zone = document.getElementById('camera-zone');
    const msg  = document.getElementById('scan-result-msg');
    const btn  = document.getElementById('btn-camera');

    msg.style.display  = 'none';
    zone.style.display = 'block';
    btn.textContent    = '✖ Stop';
    btn.classList.add('actif');
    scannerActif = true;
    dernierCode  = null;

    qrScanner = new Html5Qrcode('camera-reader');

    try {
        await qrScanner.start(
            { facingMode: 'environment' },
            {
                fps: 15,
                qrbox: { width: 260, height: 120 },
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                ]
            },
            async (decodedText) => {
                if (decodedText === dernierCode) return;
                dernierCode = decodedText;
                await fermerCamera();
                await scannerCodeBarre(decodedText);
            }
        );
    } catch (err) {
        await fermerCamera();
        erreur('Caméra inaccessible — vérifiez les permissions.');
    }
}

async function fermerCamera() {
    if (qrScanner) {
        try {
            if (qrScanner.isScanning) await qrScanner.stop();
            qrScanner.clear();
        } catch(e) {}
        qrScanner = null;
    }
    scannerActif = false;
    dernierCode  = null;
    document.getElementById('camera-zone').style.display = 'none';
    const btn = document.getElementById('btn-camera');
    btn.textContent = '📷 Caméra';
    btn.classList.remove('actif');
    scannerInput.focus();
}

// ── Panier ────────────────────────────────────
function ajouterAuPanier(id, nom, prix, stockDispo) {
    cacherResultats();
    scannerInput.value = '';
    scannerInput.focus();

    const existant = panier.find(i => i.id === id);
    if (existant) {
        if (existant.qte >= stockDispo) {
            erreur('Stock max atteint pour ' + nom + ' (' + stockDispo + ')');
            return;
        }
        existant.qte++;
    } else {
        panier.push({ id, nom, prix, qte: 1, stockDispo });
    }
    rafraichirPanier();
}

function changerQte(id, delta) {
    const item = panier.find(i => i.id === id);
    if (!item) return;
    item.qte = Math.max(1, Math.min(item.stockDispo, item.qte + delta));
    rafraichirPanier();
}

function supprimerItem(id) {
    panier = panier.filter(i => i.id !== id);
    rafraichirPanier();
}

function viderPanier() {
    panier = [];
    document.getElementById('montant-recu').value = '';
    document.getElementById('monnaie-rendue').textContent = '— FCFA';
    rafraichirPanier();
}

function rafraichirPanier() {
    const liste = document.getElementById('panier-liste');
    const total = panier.reduce((s, i) => s + i.prix * i.qte, 0);

    if (!panier.length) {
        liste.innerHTML =
            '<div class="text-center text-muted py-4">' +
            '<div style="font-size:2.5rem">🛒</div><div>Panier vide</div></div>';
    } else {
        liste.innerHTML = panier.map(item =>
            '<div class="d-flex align-items-center justify-content-between py-2 border-bottom gap-2">' +
            '<div style="flex:1;min-width:0;">' +
            '<div class="fw-semibold small">' + esc(item.nom) + '</div>' +
            '<div class="text-muted" style="font-size:0.75rem;">' +
            fmt(item.prix) + ' × ' + item.qte + ' = <strong>' + fmt(item.prix * item.qte) + ' FCFA</strong>' +
            '</div></div>' +
            '<div class="d-flex align-items-center gap-1 flex-shrink-0">' +
            '<button class="btn btn-outline-secondary btn-sm px-2 py-0" onclick="changerQte(' + item.id + ',-1)">−</button>' +
            '<span class="fw-bold px-1">' + item.qte + '</span>' +
            '<button class="btn btn-outline-secondary btn-sm px-2 py-0" onclick="changerQte(' + item.id + ',1)">+</button>' +
            '<button class="btn btn-outline-danger btn-sm px-1 py-0 ms-1" onclick="supprimerItem(' + item.id + ')">✕</button>' +
            '</div></div>'
        ).join('');
    }

    document.getElementById('total-affiche').textContent = fmt(total) + ' FCFA';
    calculerMonnaie();
}

function calculerMonnaie() {
    const total   = panier.reduce((s, i) => s + i.prix * i.qte, 0);
    const recu    = parseFloat(document.getElementById('montant-recu').value) || 0;
    const monnaie = recu - total;
    const el      = document.getElementById('monnaie-rendue');
    el.textContent = monnaie >= 0 ? fmt(monnaie) + ' FCFA' : '— FCFA';
    el.style.color = monnaie >= 0 ? '#854d0e' : '#dc2626';
}

// ── Encaissement ──────────────────────────────
async function encaisser() {
    if (!panier.length) { erreur('Panier vide !'); return; }
    const total    = panier.reduce((s, i) => s + i.prix * i.qte, 0);
    const recu     = parseFloat(document.getElementById('montant-recu').value) || 0;
    const clientId = document.getElementById('client-select').value;
    const credit   = document.getElementById('est-credit').checked;
    if (!credit && recu > 0 && recu < total) { erreur('Montant reçu insuffisant !'); return; }

    try {
        const res = await fetch(ROUTES.vente, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({
                panier:       panier.map(i => ({ id: i.id, qte: i.qte })),
                client_id:    clientId || null,
                est_credit:   credit,
                montant_recu: recu || total,
            })
        });
        const data = await res.json();
        if (!res.ok) { erreur(data.error); return; }

        document.getElementById('modal-numero').textContent = 'Vente ' + data.numero;
        document.getElementById('modal-total').textContent  = fmt(data.total) + ' FCFA';
        document.getElementById('btn-facture').href         = data.facture_url;

        const mEl = document.getElementById('modal-monnaie');
        if (data.monnaie > 0) {
            mEl.textContent   = '💵 Monnaie : ' + fmt(data.monnaie) + ' FCFA';
            mEl.style.display = 'block';
        } else {
            mEl.style.display = 'none';
        }
        new bootstrap.Modal(document.getElementById('modalSucces')).show();
    } catch(e) { erreur('Erreur réseau — réessayez'); }
}

function nouvelleVente() {
    viderPanier();
    document.getElementById('montant-recu').value  = '';
    document.getElementById('client-select').value = '';
    document.getElementById('est-credit').checked  = false;
    bootstrap.Modal.getInstance(document.getElementById('modalSucces'))?.hide();
    scannerInput.focus();
}

document.getElementById('client-select').addEventListener('change', function () {
    const credit = this.options[this.selectedIndex]?.dataset.credit === '1' && this.value;
    document.getElementById('credit-option').style.display = credit ? 'block' : 'none';
    if (!credit) document.getElementById('est-credit').checked = false;
    afficherInfoCredit();
});

function afficherInfoCredit() {
    const sel    = document.getElementById('client-select');
    const opt    = sel.options[sel.selectedIndex];
    const zone   = document.getElementById('credit-info');

    if (!sel.value || opt.dataset.credit !== '1') { zone.style.display = 'none'; return; }

    const LIMITE  = 10_000_000;
    const solde   = parseFloat(opt.dataset.solde || 0);
    const restant = Math.max(0, LIMITE - solde);
    const pct     = Math.min(100, (solde / LIMITE) * 100);
    const bloque  = opt.dataset.bloque === '1';

    let couleur = pct >= 90 ? '#dc2626' : pct >= 70 ? '#f59e0b' : '#16a34a';
    let bgZone  = pct >= 90 ? '#fef2f2' : pct >= 70 ? '#fffbeb' : '#f0fdf4';

    document.getElementById('credit-bar').style.width      = pct + '%';
    document.getElementById('credit-bar').style.background = couleur;
    document.getElementById('credit-chiffres').textContent = fmt(solde) + ' / ' + fmt(LIMITE) + ' F';
    document.getElementById('credit-chiffres').style.color = couleur;
    document.getElementById('credit-label').textContent    = bloque ? '🔒 Client bloqué' : 'Crédit utilisé';

    const el = document.getElementById('credit-restant');
    if (bloque) {
        el.textContent = '⛔ Limite atteinte — aucun crédit supplémentaire possible';
        el.style.color = '#dc2626';
        document.getElementById('est-credit').checked  = false;
        document.getElementById('est-credit').disabled = true;
    } else {
        el.textContent = '✅ Crédit restant : ' + fmt(restant) + ' FCFA';
        el.style.color = couleur;
        document.getElementById('est-credit').disabled = false;
    }

    zone.style.background = bgZone;
    zone.style.display    = 'block';
}

// ── Utilitaires ───────────────────────────────
function fmt(n)  { return new Intl.NumberFormat('fr-FR').format(Math.round(n)); }
function csrf()  { return document.querySelector('meta[name="csrf-token"]').content; }
function esc(s)  {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
function erreur(msg) {
    const t = document.createElement('div');
    t.className = 'position-fixed top-0 end-0 m-3 alert alert-danger shadow fw-semibold';
    t.style.zIndex = 9999;
    t.textContent  = '❌ ' + msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

rafraichirPanier();
</script>

@endsection
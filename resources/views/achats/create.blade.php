@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">🛍️ Nouvel achat fournisseur</h2>
    <a href="{{ route('achats.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Retour</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('achats.store') }}" method="POST" id="form-achat">
@csrf

<div class="row g-3">

    {{-- ── Colonne gauche : infos générales + lignes ── --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Entête du bon --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:.72rem; letter-spacing:1px;">
                    Informations du bon
                </h6>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Fournisseur <span class="text-danger">*</span></label>
                        <select name="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            @foreach($fournisseurs as $f)
                                <option value="{{ $f->id }}" {{ old('fournisseur_id') == $f->id ? 'selected' : '' }}>
                                    {{ $f->nom }}{{ $f->prenom ? ' '.$f->prenom : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('fournisseur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <a href="{{ route('fournisseurs.create') }}" class="small text-primary mt-1 d-inline-block">➕ Nouveau fournisseur</a>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date_achat"
                               class="form-control @error('date_achat') is-invalid @enderror"
                               value="{{ old('date_achat', date('Y-m-d')) }}" required>
                        @error('date_achat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">N° Référence</label>
                        <input type="text" name="reference" class="form-control"
                               value="{{ old('reference') }}" placeholder="Optionnel">
                    </div>
                </div>
            </div>
        </div>

        {{-- Lignes produits --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:.72rem; letter-spacing:1px;">
                    Produits réceptionnés
                </h6>

                {{-- En-tête colonnes --}}
                <div class="row g-2 mb-2 d-none d-md-flex">
                    <div class="col-md-5"><small class="text-muted fw-semibold">Produit</small></div>
                    <div class="col-md-2 text-center"><small class="text-muted fw-semibold">Qté</small></div>
                    <div class="col-md-3 text-center"><small class="text-muted fw-semibold">Prix unit. (FCFA)</small></div>
                    <div class="col-md-2 text-end"><small class="text-muted fw-semibold">Sous-total</small></div>
                </div>

                <div id="lignes-container"></div>

                <button type="button" onclick="ajouterLigne()"
                        class="btn btn-outline-primary btn-sm mt-3">
                    ➕ Ajouter un produit
                </button>
            </div>
        </div>

    </div>

    {{-- ── Colonne droite : récapitulatif ── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="position:sticky; top:80px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">📋 Récapitulatif</h6>

                <div id="recap-lignes" class="mb-3" style="font-size:.82rem; min-height:40px;">
                    <span class="text-muted">Aucun produit ajouté</span>
                </div>

                <div class="border-top pt-3 mb-4">
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>TOTAL</span>
                        <span id="recap-total" class="text-primary">0 FCFA</span>
                    </div>
                    <div class="text-muted small" id="recap-stock-info"></div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                    💾 Enregistrer le bon d'achat
                </button>
            </div>
        </div>
    </div>

</div>
</form>

@php
$produitsJson = $produits->map(fn($p) => [
    'id'       => $p->id,
    'nom'      => $p->nom,
    'stock'    => $p->quantite,
    'prix_ref' => (float) $p->prix,
]);
@endphp

<script>
const PRODUITS = @json($produitsJson);
let ligneIndex = 0;

function ajouterLigne() {
    const container = document.getElementById('lignes-container');
    const idx = ligneIndex++;

    const options = PRODUITS.map(p =>
        `<option value="${p.id}" data-stock="${p.stock}" data-prix="${p.prix_ref}">
            ${esc(p.nom)} (stock: ${p.stock})
        </option>`
    ).join('');

    const div = document.createElement('div');
    div.className = 'ligne-achat row g-2 align-items-center mb-2 p-2 rounded';
    div.style.background = '#f8fafc';
    div.innerHTML = `
        <div class="col-12 col-md-5">
            <select name="lignes[${idx}][produit_id]"
                    class="form-select form-select-sm"
                    onchange="produitChange(this, ${idx})" required>
                <option value="">— Choisir un produit —</option>
                ${options}
            </select>
        </div>
        <div class="col-4 col-md-2">
            <input type="number" name="lignes[${idx}][quantite]"
                   id="qte-${idx}"
                   class="form-control form-control-sm text-center"
                   placeholder="Qté" min="1" value="1"
                   oninput="mettreAJourRecap()" required>
        </div>
        <div class="col-5 col-md-3">
            <input type="number" name="lignes[${idx}][prix]"
                   id="prix-${idx}"
                   class="form-control form-control-sm text-center"
                   placeholder="Prix unit." min="0" step="1"
                   oninput="mettreAJourRecap()" required>
        </div>
        <div class="col-2 col-md-1 text-end">
            <span id="sous-total-${idx}" class="fw-semibold small text-primary">—</span>
        </div>
        <div class="col-1 text-end">
            <button type="button" onclick="supprimerLigne(this)"
                    class="btn btn-outline-danger btn-sm px-2 py-0">✕</button>
        </div>`;

    container.appendChild(div);
    mettreAJourRecap();
}

function produitChange(select, idx) {
    const opt = select.options[select.selectedIndex];
    const prixRef = parseFloat(opt?.dataset.prix || 0);
    if (prixRef > 0) {
        document.getElementById(`prix-${idx}`).value = prixRef;
    }
    mettreAJourRecap();
}

function supprimerLigne(btn) {
    const lignes = document.querySelectorAll('.ligne-achat');
    if (lignes.length <= 1) return;
    btn.closest('.ligne-achat').remove();
    mettreAJourRecap();
}

function mettreAJourRecap() {
    const lignes = document.querySelectorAll('.ligne-achat');
    let total = 0;
    let recapHtml = '';
    let nbProduits = 0;

    lignes.forEach(ligne => {
        const select = ligne.querySelector('select');
        const qteInput  = ligne.querySelector('input[name*="quantite"]');
        const prixInput = ligne.querySelector('input[name*="prix"]');
        const idx = qteInput?.id?.replace('qte-', '');

        if (!select?.value) return;

        const nom  = select.options[select.selectedIndex]?.text?.split(' (stock')[0]?.trim() || '';
        const qte  = parseInt(qteInput?.value) || 0;
        const prix = parseFloat(prixInput?.value) || 0;
        const sous = qte * prix;

        total += sous;
        nbProduits++;

        if (idx !== undefined) {
            const el = document.getElementById(`sous-total-${idx}`);
            if (el) el.textContent = qte && prix ? fmt(sous) + ' F' : '—';
        }

        if (qte > 0 && prix > 0) {
            recapHtml += `<div class="d-flex justify-content-between mb-1">
                <span class="text-truncate me-2">${esc(nom)} × ${qte}</span>
                <span class="fw-semibold flex-shrink-0">${fmt(sous)} F</span>
            </div>`;
        }
    });

    document.getElementById('recap-lignes').innerHTML =
        recapHtml || '<span class="text-muted">Aucun produit ajouté</span>';
    document.getElementById('recap-total').textContent = fmt(total) + ' FCFA';
    document.getElementById('recap-stock-info').textContent =
        nbProduits > 0 ? `${nbProduits} produit(s) • stocks mis à jour à l'enregistrement` : '';
}

const fmt = n => new Intl.NumberFormat('fr-FR').format(Math.round(n));
const esc = s => String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

// Ajouter une première ligne au chargement
ajouterLigne();
</script>

@endsection

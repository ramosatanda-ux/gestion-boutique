@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">🛒 Nouvelle vente</h2>
    <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Retour</a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('ventes.store') }}" id="form-vente">
@csrf

<div class="row g-3">

    {{-- ── COLONNE GAUCHE : client + produits ── --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Client --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">👤 Client</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Choisir un client existant</label>
                    <select id="client-select" name="client_id" class="form-select">
                        <option value="">— Client de passage (sans compte) —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}"
                                    data-nom="{{ $c->nom }}"
                                    data-telephone="{{ $c->telephone ?? '' }}"
                                    data-adresse="{{ $c->adresse ?? '' }}"
                                    data-credit="{{ $c->a_credit ? 1 : 0 }}"
                                    data-solde="{{ $c->solde }}">
                                {{ $c->nom }}
                                @if($c->solde > 0) — dette : {{ number_format($c->solde,0,',',' ') }} FCFA @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom_client" id="nom-client"
                               class="form-control form-control-sm @error('nom_client') is-invalid @enderror"
                               value="{{ old('nom_client') }}" required placeholder="Nom du client">
                        @error('nom_client')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Téléphone</label>
                        <input type="text" name="telephone" id="telephone"
                               class="form-control form-control-sm"
                               value="{{ old('telephone') }}" placeholder="Optionnel">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Adresse</label>
                        <input type="text" name="adresse" id="adresse"
                               class="form-control form-control-sm"
                               value="{{ old('adresse') }}" placeholder="Optionnel">
                    </div>
                </div>

                {{-- Option crédit (visible seulement si client avec crédit autorisé) --}}
                <div id="credit-zone" class="mt-3" style="display:none;">
                    <div class="form-check">
                        <input type="checkbox" name="est_credit" id="est-credit" class="form-check-input" value="1">
                        <label class="form-check-label text-danger fw-semibold" for="est-credit">
                            Vente à crédit
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Produits --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">🧾 Produits</h5>

                <div id="produits-container" class="d-flex flex-column gap-2">
                    {{-- Ligne générée par JS --}}
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm mt-3" onclick="ajouterLigne()">
                    ➕ Ajouter un produit
                </button>
            </div>
        </div>

    </div>

    {{-- ── COLONNE DROITE : total + enregistrer ── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="position:sticky; top:80px;">
            <div class="card-body">
                <h5 class="fw-bold mb-3">💰 Récapitulatif</h5>

                <div id="recap-liste" class="mb-3" style="font-size:.85rem; min-height:40px;">
                    <span class="text-muted">Aucun produit sélectionné</span>
                </div>

                <div class="d-flex justify-content-between text-muted border-top pt-2" style="font-size:.9rem;">
                    <span>Sous-total</span>
                    <span id="sous-total-affiche">0 FCFA</span>
                </div>

                {{-- Réduction --}}
                <div class="mt-2 mb-2">
                    <label class="form-label small fw-semibold mb-1">Réduction</label>
                    <div class="input-group input-group-sm">
                        <input type="number" id="reduction-pct" min="0" max="100" step="0.01"
                               class="form-control" placeholder="%" value="0"
                               oninput="appliquerPourcentage()">
                        <span class="input-group-text">%</span>
                        <input type="number" id="reduction-montant" name="reduction" min="0" step="1"
                               class="form-control" placeholder="FCFA" value="0"
                               oninput="mettreAJourTotal()">
                        <span class="input-group-text">FCFA</span>
                    </div>
                    <div class="text-end text-danger small mt-1" id="reduction-affiche" style="display:none;"></div>
                </div>

                <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mb-4">
                    <span>TOTAL NET</span>
                    <span id="total-affiche" class="text-primary">0 FCFA</span>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                    💾 Enregistrer la vente
                </button>
                <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                    🖥️ Utiliser le POS à la place
                </a>
            </div>
        </div>
    </div>

</div>
</form>

{{-- Template d'une ligne produit (caché) --}}
@php
    $produitsJson = $produits->map(fn($p) => [
        'id'       => $p->id,
        'nom'      => $p->nom,
        'prix'     => (float) $p->prix,
        'quantite' => $p->quantite,
    ]);
@endphp

<script>
const PRODUITS = @json($produitsJson);

// Remplir les infos client automatiquement
document.getElementById('client-select').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];

    document.getElementById('nom-client').value = opt.dataset.nom  || '';
    document.getElementById('telephone').value  = opt.dataset.telephone || '';
    document.getElementById('adresse').value    = opt.dataset.adresse   || '';

    const creditZone = document.getElementById('credit-zone');
    const creditCheck = document.getElementById('est-credit');

    if (this.value && opt.dataset.credit === '1') {
        creditZone.style.display = 'block';
    } else {
        creditZone.style.display = 'none';
        creditCheck.checked = false;
    }

    if (!this.value) {
        document.getElementById('nom-client').value = '';
        document.getElementById('telephone').value  = '';
        document.getElementById('adresse').value    = '';
    }

    mettreAJourTotal();
});

let ligneIndex = 0;

function ajouterLigne() {
    const container = document.getElementById('produits-container');
    const idx = ligneIndex++;

    const options = PRODUITS.map(p =>
        `<option value="${p.id}" data-prix="${p.prix}" data-stock="${p.quantite}">
            ${esc(p.nom)} — ${fmt(p.prix)} FCFA (stock : ${p.quantite})
        </option>`
    ).join('');

    const div = document.createElement('div');
    div.className = 'row g-2 align-items-center produit-ligne';
    div.innerHTML =
        '<div class="col-7">' +
            '<select name="produits[]" class="form-select form-select-sm" onchange="mettreAJourTotal()">' +
                options +
            '</select>' +
        '</div>' +
        '<div class="col-3">' +
            '<input type="number" name="quantites[]" class="form-control form-control-sm" ' +
                   'value="1" min="1" oninput="mettreAJourTotal()">' +
        '</div>' +
        '<div class="col-2 text-end">' +
            '<button type="button" class="btn btn-outline-danger btn-sm" onclick="supprimerLigne(this)">✕</button>' +
        '</div>';

    container.appendChild(div);
    mettreAJourTotal();
}

function supprimerLigne(btn) {
    const lignes = document.querySelectorAll('.produit-ligne');
    if (lignes.length <= 1) return;
    btn.closest('.produit-ligne').remove();
    mettreAJourTotal();
}

function mettreAJourTotal() {
    const lignes = document.querySelectorAll('.produit-ligne');
    let sousTotal = 0;
    let recapHtml = '';

    lignes.forEach(ligne => {
        const sel = ligne.querySelector('select');
        const qteInput = ligne.querySelector('input[type=number]');
        if (!sel || !qteInput) return;

        const opt  = sel.options[sel.selectedIndex];
        const prix = parseFloat(opt?.dataset.prix || 0);
        const qte  = parseInt(qteInput.value) || 0;
        const nom  = opt?.text?.split(' —')[0]?.trim() || '';
        const sous = prix * qte;
        sousTotal += sous;

        if (qte > 0)
            recapHtml += `<div class="d-flex justify-content-between">
                <span>${esc(nom)} × ${qte}</span>
                <span>${fmt(sous)} FCFA</span>
            </div>`;
    });

    document.getElementById('recap-liste').innerHTML = recapHtml ||
        '<span class="text-muted">Aucun produit sélectionné</span>';
    document.getElementById('sous-total-affiche').textContent = fmt(sousTotal) + ' FCFA';

    const reduction = Math.min(parseInt(document.getElementById('reduction-montant').value) || 0, sousTotal);
    document.getElementById('reduction-montant').max = sousTotal;

    const totalNet = sousTotal - reduction;
    document.getElementById('total-affiche').textContent = fmt(totalNet) + ' FCFA';

    const affReduction = document.getElementById('reduction-affiche');
    if (reduction > 0) {
        affReduction.textContent = '− ' + fmt(reduction) + ' FCFA';
        affReduction.style.display = '';
    } else {
        affReduction.style.display = 'none';
    }
}

function appliquerPourcentage() {
    const pct = parseFloat(document.getElementById('reduction-pct').value) || 0;
    const lignes = document.querySelectorAll('.produit-ligne');
    let sousTotal = 0;
    lignes.forEach(ligne => {
        const sel = ligne.querySelector('select');
        const qteInput = ligne.querySelector('input[type=number]');
        if (!sel || !qteInput) return;
        const prix = parseFloat(sel.options[sel.selectedIndex]?.dataset.prix || 0);
        const qte  = parseInt(qteInput.value) || 0;
        sousTotal += prix * qte;
    });
    document.getElementById('reduction-montant').value = Math.round(sousTotal * pct / 100);
    mettreAJourTotal();
}

function fmt(n) { return new Intl.NumberFormat('fr-FR').format(Math.round(n)); }
function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Ajouter une première ligne au chargement
ajouterLigne();
</script>

@endsection

<div class="card-body d-flex flex-column" style="min-height: 300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">🛒 Panier</h5>
        <button class="btn btn-outline-danger btn-sm" onclick="viderPanier()">🗑️ Vider</button>
    </div>

    <div class="panier-liste flex-grow-1" id="panier-liste">
        <div class="text-center text-muted py-4">
            <div style="font-size:2.5rem">🛒</div>
            <div>Panier vide</div>
        </div>
    </div>

    {{-- Total --}}
    <div class="mt-3">
        <div class="total-zone p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fs-6">TOTAL</span>
                <span class="fs-4 fw-bold total-affiche">0 FCFA</span>
            </div>
        </div>

        {{-- Montant reçu & monnaie --}}
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

        {{-- Client --}}
        <select id="client-select" class="form-select form-select-sm mb-2">
            <option value="">👤 Client comptoir</option>
            @foreach($clients as $c)
                <option value="{{ $c->id }}" data-credit="{{ $c->a_credit ? 1 : 0 }}">
                    {{ $c->nom }} (dette: {{ number_format($c->solde,0,',',' ') }} F)
                </option>
            @endforeach
        </select>

        <div id="credit-option" style="display:none;" class="mb-2">
            <div class="form-check">
                <input type="checkbox" id="est-credit" class="form-check-input">
                <label class="form-check-label text-danger small fw-semibold">Vente à crédit</label>
            </div>
        </div>

        <button class="btn btn-encaisser btn-success w-100 py-2 text-white fw-bold"
                onclick="encaisser()">
            💰 Encaisser
        </button>
    </div>
</div>
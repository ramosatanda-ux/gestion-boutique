@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">👥 Nouveau client</h2>
    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Retour</a>
</div>

<div class="row g-3">
<div class="col-lg-7">
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('clients.store') }}">
            @csrf

            {{-- Infos principales --}}
            <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                Informations
            </h6>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nom complet <span class="text-danger">*</span>
                </label>
                <input type="text" name="nom"
                       class="form-control @error('nom') is-invalid @enderror"
                       value="{{ old('nom') }}" placeholder="Ex: Koné Amadou" required autofocus>
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="telephone"
                           class="form-control @error('telephone') is-invalid @enderror"
                           value="{{ old('telephone') }}" placeholder="Ex: 07 00 00 00 00">
                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Adresse</label>
                    <input type="text" name="adresse" class="form-control"
                           value="{{ old('adresse') }}" placeholder="Quartier, ville...">
                </div>
            </div>

            <hr class="my-4">

            {{-- Type et crédit --}}
            <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                Type de client
            </h6>

            <div class="card border bg-light mb-3 p-3">
                <div class="form-check mb-0">
                    <input type="checkbox" name="est_particulier" id="est_particulier"
                           class="form-check-input" value="1"
                           {{ old('est_particulier') ? 'checked' : '' }}
                           onchange="toggleCredit(this)">
                    <label class="form-check-label fw-semibold" for="est_particulier">
                        Client particulier
                    </label>
                    <div class="text-muted small mt-1">
                        Un client particulier peut être autorisé à acheter à crédit (paiement différé).
                    </div>
                </div>
            </div>

            <div id="credit-zone" style="{{ old('est_particulier') ? '' : 'display:none;' }}">
                <div class="card border border-warning bg-warning bg-opacity-10 p-3 mb-3">
                    <div class="form-check mb-0">
                        <input type="checkbox" name="a_credit" id="a_credit"
                               class="form-check-input" value="1"
                               {{ old('a_credit') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-warning" for="a_credit">
                            ⚠️ Autoriser les achats à crédit
                        </label>
                        <div class="text-muted small mt-1">
                            Ce client pourra payer ses achats plus tard. Sa dette sera suivie automatiquement.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4">💾 Enregistrer</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Annuler</a>
            </div>

        </form>
    </div>
</div>
</div>

{{-- Aide contextuelle --}}
<div class="col-lg-5">
    <div class="card border-0 shadow-sm bg-light">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">💡 Guide</h6>
            <div class="d-flex gap-2 mb-3">
                <div style="font-size:1.5rem;">👤</div>
                <div>
                    <div class="fw-semibold small">Client simple</div>
                    <div class="text-muted small">Paie comptant à chaque achat. Pas de suivi de dette.</div>
                </div>
            </div>
            <div class="d-flex gap-2 mb-3">
                <div style="font-size:1.5rem;">🤝</div>
                <div>
                    <div class="fw-semibold small">Client particulier</div>
                    <div class="text-muted small">Peut être autorisé au crédit. Son historique d'achats est suivi.</div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <div style="font-size:1.5rem;">⚠️</div>
                <div>
                    <div class="fw-semibold small">Crédit autorisé</div>
                    <div class="text-muted small">La dette est plafonnée à 10 000 000 FCFA. Au-delà, les ventes à crédit sont bloquées.</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function toggleCredit(checkbox) {
    const zone = document.getElementById('credit-zone');
    const creditCheck = document.getElementById('a_credit');
    zone.style.display = checkbox.checked ? 'block' : 'none';
    if (!checkbox.checked) creditCheck.checked = false;
}
</script>

@endsection

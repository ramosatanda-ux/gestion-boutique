@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">✏️ Modifier un client</h2>
        <span class="text-muted small">{{ $client->nom }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-outline-primary btn-sm">👁️ Voir</a>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Retour</a>
    </div>
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

        <form method="POST" action="{{ route('clients.update', $client->id) }}">
            @csrf
            @method('PUT')

            <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                Informations
            </h6>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nom complet <span class="text-danger">*</span>
                </label>
                <input type="text" name="nom"
                       class="form-control @error('nom') is-invalid @enderror"
                       value="{{ old('nom', $client->nom) }}" required>
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="telephone"
                           class="form-control @error('telephone') is-invalid @enderror"
                           value="{{ old('telephone', $client->telephone) }}"
                           placeholder="Ex: 07 00 00 00 00">
                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Adresse</label>
                    <input type="text" name="adresse" class="form-control"
                           value="{{ old('adresse', $client->adresse) }}"
                           placeholder="Quartier, ville...">
                </div>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.72rem; letter-spacing:1px;">
                Type de client
            </h6>

            {{-- Solde actuel (lecture seule, informatif) --}}
            @if($client->solde > 0)
            <div class="alert alert-warning py-2 mb-3 d-flex justify-content-between align-items-center">
                <span class="small fw-semibold">⚠️ Ce client a une dette en cours</span>
                <span class="fw-bold">{{ number_format($client->solde, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif

            <div class="card border bg-light mb-3 p-3">
                <div class="form-check mb-0">
                    <input type="checkbox" name="est_particulier" id="est_particulier"
                           class="form-check-input" value="1"
                           {{ old('est_particulier', $client->est_particulier) ? 'checked' : '' }}
                           onchange="toggleCredit(this)">
                    <label class="form-check-label fw-semibold" for="est_particulier">
                        Client particulier
                    </label>
                    <div class="text-muted small mt-1">
                        Peut être autorisé à acheter à crédit.
                    </div>
                </div>
            </div>

            @php $showCredit = old('est_particulier', $client->est_particulier); @endphp
            <div id="credit-zone" style="{{ $showCredit ? '' : 'display:none;' }}">
                <div class="card border border-warning bg-warning bg-opacity-10 p-3 mb-3">
                    <div class="form-check mb-0">
                        <input type="checkbox" name="a_credit" id="a_credit"
                               class="form-check-input" value="1"
                               {{ old('a_credit', $client->a_credit) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-warning" for="a_credit">
                            ⚠️ Autoriser les achats à crédit
                        </label>
                        <div class="text-muted small mt-1">
                            Dette plafonnée à 10 000 000 FCFA.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">💾 Enregistrer</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Annuler</a>
            </div>

        </form>
    </div>
</div>
</div>

{{-- Résumé rapide --}}
<div class="col-lg-5">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">📋 Résumé</h6>

            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Type</span>
                <span class="fw-semibold">
                    {{ $client->est_particulier ? 'Particulier' : 'Simple' }}
                </span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Crédit autorisé</span>
                <span class="fw-semibold {{ $client->a_credit ? 'text-success' : 'text-muted' }}">
                    {{ $client->a_credit ? 'Oui' : 'Non' }}
                </span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Dette actuelle</span>
                <span class="fw-semibold {{ $client->solde > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($client->solde, 0, ',', ' ') }} FCFA
                </span>
            </div>
            <div class="d-flex justify-content-between mb-3 small">
                <span class="text-muted">Inscrit le</span>
                <span class="fw-semibold">{{ $client->created_at->format('d/m/Y') }}</span>
            </div>

            <hr>

            <div class="d-flex flex-column gap-2 mt-3">
                @if($client->solde > 0)
                <a href="{{ route('clients.payer.form', $client->id) }}"
                   class="btn btn-success btn-sm w-100">💵 Enregistrer un paiement</a>
                @endif
                <a href="{{ route('clients.show', $client->id) }}"
                   class="btn btn-outline-primary btn-sm w-100">📜 Voir l'historique complet</a>
                @if($client->solde == 0)
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce client ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm w-100">🗑️ Supprimer ce client</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<script>
function toggleCredit(checkbox) {
    const zone  = document.getElementById('credit-zone');
    const check = document.getElementById('a_credit');
    zone.style.display = checkbox.checked ? 'block' : 'none';
    if (!checkbox.checked) check.checked = false;
}
</script>

@endsection

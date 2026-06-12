@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">📦 Ajouter un produit</h2>
    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">⬅ Retour</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:620px;">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('produits.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nom du produit <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                       value="{{ old('nom') }}" placeholder="Ex: Riz 25kg" required>
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- NOUVEAU : Code-barres pour le POS --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Code-barres</label>
                <div class="input-group">
                    <span class="input-group-text">📦</span>
                    <input type="text" name="code_barre" class="form-control @error('code_barre') is-invalid @enderror"
                           value="{{ old('code_barre') }}"
                           placeholder="Scannez ou saisissez le code-barres"
                           id="code-barre-input">
                </div>
                <div class="form-text">Optionnel — utilisé pour la recherche rapide au point de vente.</div>
                @error('code_barre')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <input type="text" name="description" class="form-control"
                       value="{{ old('description') }}" placeholder="Optionnel">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="prix" class="form-control @error('prix') is-invalid @enderror"
                           value="{{ old('prix') }}" min="0" required>
                    @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Quantité initiale <span class="text-danger">*</span></label>
                    <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite', 0) }}" min="0" required>
                    @error('quantite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Stock minimum (alerte)</label>
                <input type="number" name="stock_minimum" class="form-control"
                       value="{{ old('stock_minimum', 5) }}" min="0">
                <div class="form-text">Alerte quand le stock descend sous ce seuil.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">💾 Enregistrer</button>
                <a href="{{ route('produits.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection

@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">✏️ Modifier un fournisseur</h2>
    <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">⬅ Retour</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:560px;">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('fournisseurs.update', $fournisseur->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                           value="{{ old('nom', $fournisseur->nom) }}" required>
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Prénom</label>
                    <input type="text" name="prenom" class="form-control"
                           value="{{ old('prenom', $fournisseur->prenom) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Téléphone</label>
                <input type="text" name="telephone" class="form-control"
                       value="{{ old('telephone', $fournisseur->telephone) }}">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Adresse</label>
                <input type="text" name="adresse" class="form-control"
                       value="{{ old('adresse', $fournisseur->adresse) }}">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">💾 Enregistrer</button>
                <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection
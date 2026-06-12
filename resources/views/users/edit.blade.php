@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">✏️ Modifier un utilisateur</h2>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">⬅ Retour</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:520px;">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Laisser vide pour ne pas changer">
                <div class="form-text">Minimum 8 caractères. Laisser vide pour conserver l'actuel.</div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                <select name="role" class="form-select">
                    <option value="user"      {{ old('role', $user->role) === 'user'      ? 'selected' : '' }}>👤 Vendeur</option>
                    <option value="caissiere" {{ old('role', $user->role) === 'caissiere' ? 'selected' : '' }}>💳 Caissière</option>
                    <option value="admin"     {{ old('role', $user->role) === 'admin'     ? 'selected' : '' }}>🔑 Administrateur</option>
                </select>
                <div class="form-text">
                    Vendeur : ventes + clients + POS ·
                    Caissière : POS uniquement ·
                    Admin : accès complet
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">💾 Enregistrer</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection
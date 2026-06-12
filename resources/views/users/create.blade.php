@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">➕ Ajouter un utilisateur</h2>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Retour</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:520px;">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Ex: Koné Ibrahim" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="exemple@mail.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="password-input"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimum 8 caractères" required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="toggleMdp()" title="Afficher/masquer">👁️</button>
                </div>
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check border rounded p-2 {{ old('role','user') === 'user' ? 'border-primary bg-primary bg-opacity-10' : '' }}">
                        <input type="radio" name="role" id="role-user" value="user" class="form-check-input"
                               {{ old('role', 'user') === 'user' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="role-user" style="cursor:pointer;">
                            <span class="badge bg-primary me-1">👤</span>
                            <strong>Vendeur</strong>
                            <div class="text-muted" style="font-size:.75rem;">Accès ventes, clients et POS</div>
                        </label>
                    </div>
                    <div class="form-check border rounded p-2 {{ old('role') === 'caissiere' ? 'border-info bg-info bg-opacity-10' : '' }}">
                        <input type="radio" name="role" id="role-caissiere" value="caissiere" class="form-check-input"
                               {{ old('role') === 'caissiere' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="role-caissiere" style="cursor:pointer;">
                            <span class="badge bg-info text-dark me-1">💳</span>
                            <strong>Caissière</strong>
                            <div class="text-muted" style="font-size:.75rem;">Accès POS uniquement</div>
                        </label>
                    </div>
                    <div class="form-check border rounded p-2 {{ old('role') === 'admin' ? 'border-danger bg-danger bg-opacity-10' : '' }}">
                        <input type="radio" name="role" id="role-admin" value="admin" class="form-check-input"
                               {{ old('role') === 'admin' ? 'checked' : '' }}>
                        <label class="form-check-label w-100" for="role-admin" style="cursor:pointer;">
                            <span class="badge bg-danger me-1">🔑</span>
                            <strong>Administrateur</strong>
                            <div class="text-muted" style="font-size:.75rem;">Accès complet à toute l'application</div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">💾 Créer le compte</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMdp() {
    const input = document.getElementById('password-input');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

@endsection

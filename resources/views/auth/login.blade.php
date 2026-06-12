<x-guest-layout>

    <h4 class="fw-bold mb-1" style="color:#0f172a;">Connexion</h4>
    <p class="text-muted mb-4" style="font-size:.88rem;">Entrez vos identifiants pour accéder à votre espace.</p>

    {{-- Message de statut (ex: mot de passe réinitialisé) --}}
    @if(session('status'))
        <div class="alert alert-success py-2 mb-3">{{ session('status') }}</div>
    @endif

    {{-- Erreurs globales --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold" style="font-size:.88rem;">
                Adresse email
            </label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="exemple@mail.com"
                   required autofocus autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold mb-0" style="font-size:.88rem;">
                    Mot de passe
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-decoration-none" style="font-size:.78rem; color:#3b82f6;">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required autocomplete="current-password">
                <button type="button" class="btn btn-outline-secondary"
                        onclick="toggleMdp()" title="Afficher / masquer">
                    👁️
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" id="remember_me" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label" style="font-size:.85rem;">
                Se souvenir de moi
            </label>
        </div>

        <button type="submit" class="btn w-100 py-2 fw-bold text-white"
                style="background: linear-gradient(135deg,#1e3a5f,#3b82f6); border:none; border-radius:10px; font-size:.95rem;">
            Se connecter →
        </button>

    </form>

    <script>
    function toggleMdp() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    </script>

</x-guest-layout>

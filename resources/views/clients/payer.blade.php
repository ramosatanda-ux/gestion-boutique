@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">💵 Enregistrer un paiement</h2>
    <a href="{{ route('clients.show', $client->id) }}" class="btn btn-outline-secondary btn-sm">⬅ Retour</a>
</div>

<div class="row g-3 justify-content-center">
    <div class="col-md-6">

        {{-- Résumé client --}}
        <div class="card border-0 shadow-sm mb-3"
             style="background: linear-gradient(135deg,#0f172a,#1e3a5f); color:white; border-radius:14px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold fs-5">{{ $client->nom }}</div>
                        <div style="color:#94a3b8; font-size:.85rem;">{{ $client->telephone ?? 'Sans téléphone' }}</div>
                    </div>
                    <div class="text-end">
                        <div style="color:#94a3b8; font-size:.8rem;">Dette actuelle</div>
                        <div class="fw-bold fs-4 text-danger">{{ number_format($client->solde, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('clients.payer', $client->id) }}">
                    @csrf

                    <label class="form-label fw-semibold">Montant à encaisser (FCFA)</label>
                    <input type="number" name="montant" id="montant-input"
                           class="form-control form-control-lg text-center fw-bold mb-3 @error('montant') is-invalid @enderror"
                           placeholder="0" min="1" max="{{ $client->solde }}"
                           value="{{ old('montant') }}" required autofocus>
                    @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    {{-- Boutons de montant rapide --}}
                    <div class="mb-3">
                        <div class="text-muted small mb-2">Montants rapides :</div>
                        <div class="d-flex gap-2 flex-wrap">
                            @php
                                $montants = [
                                    '25%'  => round($client->solde * 0.25),
                                    '50%'  => round($client->solde * 0.50),
                                    '75%'  => round($client->solde * 0.75),
                                    'Tout' => $client->solde,
                                ];
                            @endphp
                            @foreach($montants as $label => $val)
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        onclick="document.getElementById('montant-input').value = {{ $val }}; calculerReste();">
                                    {{ $label }}<br>
                                    <small>{{ number_format($val, 0, ',', ' ') }} F</small>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Reste après paiement --}}
                    <div class="alert alert-light border d-flex justify-content-between align-items-center py-2 mb-4">
                        <span class="text-muted small">Reste après paiement :</span>
                        <span id="reste-affiche" class="fw-bold">{{ number_format($client->solde, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold fs-5">
                        ✅ Valider le paiement
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
const dette = {{ $client->solde }};

function calculerReste() {
    const montant = parseFloat(document.getElementById('montant-input').value) || 0;
    const reste   = Math.max(0, dette - montant);
    const el      = document.getElementById('reste-affiche');
    el.textContent = new Intl.NumberFormat('fr-FR').format(reste) + ' FCFA';
    el.className   = reste === 0 ? 'fw-bold text-success' : reste < dette ? 'fw-bold text-warning' : 'fw-bold';
}

document.getElementById('montant-input').addEventListener('input', calculerReste);
</script>

@endsection

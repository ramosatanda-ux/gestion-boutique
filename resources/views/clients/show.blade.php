@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-0">👤 {{ $client->nom }}</h2>
        <span class="text-muted small">
            {{ $client->est_particulier ? 'Client particulier' : 'Client simple' }}
            @if($client->telephone) · {{ $client->telephone }} @endif
        </span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($client->solde > 0)
            <a href="{{ route('clients.payer.form', $client->id) }}" class="btn btn-success btn-sm">
                💵 Enregistrer un paiement
            </a>
        @endif
        <a href="{{ route('clients.historique.pdf', $client->id) }}" target="_blank"
           class="btn btn-outline-danger btn-sm">🧾 PDF</a>
        <a href="{{ route('clients.debiteurs') }}" class="btn btn-outline-secondary btn-sm">⬅ Débiteurs</a>
    </div>
</div>

{{-- Barre de crédit (uniquement pour les clients avec crédit autorisé) --}}
@if($client->a_credit)
@php
    $limite   = 10_000_000;
    $pct      = min(100, ($client->solde / $limite) * 100);
    $restant  = max(0, $limite - $client->solde);
    $couleur  = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
@endphp
<div class="card border-0 shadow-sm mb-4 border-start border-{{ $couleur }} border-3">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-semibold small">
                @if($pct >= 100) 🔒 Limite de crédit atteinte
                @elseif($pct >= 90) ⚠️ Crédit presque épuisé
                @else 💳 Crédit autorisé
                @endif
            </span>
            <span class="fw-bold small text-{{ $couleur }}">
                {{ number_format($client->solde, 0, ',', ' ') }} / {{ number_format($limite, 0, ',', ' ') }} FCFA
            </span>
        </div>
        <div class="progress mb-1" style="height:8px;">
            <div class="progress-bar bg-{{ $couleur }}" style="width:{{ $pct }}%;"></div>
        </div>
        <div class="d-flex justify-content-between">
            <small class="text-muted">0 FCFA</small>
            <small class="text-{{ $couleur }} fw-semibold">
                Restant : {{ number_format($restant, 0, ',', ' ') }} FCFA
            </small>
            <small class="text-muted">10 000 000 FCFA</small>
        </div>
    </div>
</div>
@endif

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100 {{ $client->solde > 0 ? 'border-start border-danger border-3' : '' }}">
            <div class="card-body py-3">
                <div class="fw-bold fs-5 {{ $client->solde > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($client->solde, 0, ',', ' ') }} FCFA
                </div>
                <small class="text-muted">Dette actuelle</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fw-bold fs-5 text-success">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</div>
                <small class="text-muted">Total payé</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fw-bold fs-5 text-primary">{{ number_format($totalAchat, 0, ',', ' ') }} FCFA</div>
                <small class="text-muted">Total achats à crédit</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fw-bold fs-5">{{ $client->ventes->count() }}</div>
                <small class="text-muted">Ventes à crédit</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Ventes à crédit --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">🛒 Ventes à crédit</h5>
                @forelse($client->ventes as $vente)
                <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                    <div>
                        <div class="small fw-semibold">{{ $vente->numero }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ $vente->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-danger small">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</div>
                        <a href="{{ route('ventes.facture', $vente->id) }}" target="_blank"
                           class="btn btn-outline-secondary btn-sm py-0 px-1 mt-1" style="font-size:.7rem;">
                            🧾 Facture
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center mt-3">Aucune vente à crédit</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Paiements reçus --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">💵 Paiements reçus</h5>
                @forelse($client->paiements as $paiement)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="text-muted small">
                        {{ $paiement->created_at->format('d/m/Y à H:i') }}
                    </div>
                    <div class="fw-bold text-success">
                        + {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                @empty
                <p class="text-muted text-center mt-3">Aucun paiement enregistré</p>
                @endforelse

                @if($client->paiements->count())
                <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-1">
                    <span>Total payé</span>
                    <span class="text-success">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">💰 Ventes</h2>
    <a href="{{ route('ventes.create') }}" class="btn btn-primary btn-sm">➕ Nouvelle vente</a>
</div>

{{-- Filtres --}}
<form method="GET" action="{{ route('ventes.index') }}" class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Recherche</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Client, numéro..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Du</label>
                <input type="date" name="date_debut" class="form-control form-control-sm"
                       value="{{ $dateDebut ?? '' }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Au</label>
                <input type="date" name="date_fin" class="form-control form-control-sm"
                       value="{{ $dateFin ?? '' }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="comptant" {{ ($type ?? '') === 'comptant' ? 'selected' : '' }}>Comptant</option>
                    <option value="credit"   {{ ($type ?? '') === 'credit'   ? 'selected' : '' }}>Crédit</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-grow-1">🔍 Filtrer</button>
                @if($search || $dateDebut || $dateFin || $type)
                    <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary btn-sm" title="Effacer">✕</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Résumé des résultats --}}
@if($search || $dateDebut || $dateFin || $type)
<div class="alert alert-info py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
    <span>
        <strong>{{ $ventes->total() }}</strong> vente(s) trouvée(s)
        @if($dateDebut || $dateFin)
            du <strong>{{ $dateDebut ? \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') : '…' }}</strong>
            au <strong>{{ $dateFin   ? \Carbon\Carbon::parse($dateFin)->format('d/m/Y')   : 'aujourd\'hui' }}</strong>
        @endif
    </span>
    <span class="fw-bold">Total : {{ number_format($totalFiltré, 0, ',', ' ') }} FCFA</span>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>N° Vente</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventes as $vente)
                    <tr>
                        <td><span class="text-muted small">{{ $vente->numero ?? 'V-'.$vente->id }}</span></td>
                        <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $vente->nom_client }}</td>
                        <td class="fw-semibold">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($vente->est_credit)
                                <span class="badge bg-danger">Crédit</span>
                            @else
                                <span class="badge bg-success">Comptant</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('ventes.facture', $vente->id) }}" target="_blank"
                               class="btn btn-success btn-sm">🧾 Facture</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Aucune vente trouvée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <small class="text-muted">
        {{ $ventes->total() }} vente(s) — page {{ $ventes->currentPage() }}/{{ $ventes->lastPage() }}
    </small>
    {{ $ventes->links() }}
</div>

@endsection

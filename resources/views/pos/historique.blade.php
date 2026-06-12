@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-0">🧾 Mes ventes</h2>
        <span class="text-muted small">{{ auth()->user()->name }}</span>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">🖥️ Retour au POS</a>
</div>

{{-- KPIs du jour --}}
<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-3">
            <div class="card-body py-3">
                <div class="text-muted small">Ventes aujourd'hui</div>
                <div class="fw-bold fs-5 text-success">
                    {{ number_format($totalAujourdhui, 0, ',', ' ') }} FCFA
                </div>
                <div class="text-muted" style="font-size:.75rem;">{{ $nbAujourdhui }} transaction(s)</div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small">Total (toutes périodes)</div>
                <div class="fw-bold fs-5">
                    {{ number_format($ventes->total(), 0, ',', ' ') }} vente(s)
                </div>
                <div class="text-muted" style="font-size:.75rem;">dans l'historique</div>
            </div>
        </div>
    </div>
</div>

{{-- Liste des ventes --}}
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
                        <th>Facture</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventes as $vente)
                    <tr>
                        <td>
                            <span class="text-muted small">{{ $vente->numero }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $vente->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $vente->created_at->format('H:i') }}</div>
                        </td>
                        <td>{{ $vente->nom_client }}</td>
                        <td class="fw-bold text-primary">
                            {{ number_format($vente->total, 0, ',', ' ') }} FCFA
                        </td>
                        <td>
                            @if($vente->est_credit)
                                <span class="badge bg-danger">Crédit</span>
                            @else
                                <span class="badge bg-success">Comptant</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('ventes.facture', $vente->id) }}"
                               target="_blank"
                               class="btn btn-outline-danger btn-sm">
                                🧾 PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <div style="font-size:2.5rem;">🧾</div>
                            <p class="mt-2">Aucune vente enregistrée pour l'instant</p>
                            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
                                🖥️ Aller au POS
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($ventes->lastPage() > 1)
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <small class="text-muted">
        {{ $ventes->total() }} vente(s) — page {{ $ventes->currentPage() }}/{{ $ventes->lastPage() }}
    </small>
    {{ $ventes->links() }}
</div>
@endif

@endsection

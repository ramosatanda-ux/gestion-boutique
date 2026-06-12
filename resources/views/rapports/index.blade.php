@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">📊 Rapports</h2>
</div>

{{-- ── Filtre par période ─────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('rapports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date début</label>
                <input type="date" name="date_debut" class="form-control" value="{{ $dateDebut }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date fin</label>
                <input type="date" name="date_fin" class="form-control" value="{{ $dateFin }}">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">🔍 Filtrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ── KPIs ───────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Ventes période</div>
            <div class="fw-bold fs-5 text-success">{{ number_format($totalVentes, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Achats période</div>
            <div class="fw-bold fs-5 text-primary">{{ number_format($totalAchats, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Bénéfice</div>
            <div class="fw-bold fs-5 {{ $benefice >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($benefice, 0, ',', ' ') }} FCFA
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-muted small">Nb ventes</div>
            <div class="fw-bold fs-5">{{ $nbVentes }}</div>
        </div>
    </div>
</div>

{{-- ── Graphique + Top produits ───────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">📈 Ventes par jour</h5>
                <canvas id="ventesChart" height="130"></canvas>
            </div>
        </div>
    </div>
    
   
    
</div>

{{-- ── Boutons d'export ───────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3">⬇️ Exporter</h5>
        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('rapports.export.ventes') }}?date_debut={{ $dateDebut }}&date_fin={{ $dateFin }}"
               class="btn btn-success">
                📊 Ventes Excel
            </a>

            <a href="{{ route('rapports.export.stocks') }}"
               class="btn btn-primary">
                📦 Stocks Excel
            </a>

            <a href="{{ route('rapports.export.complet') }}?date_debut={{ $dateDebut }}&date_fin={{ $dateFin }}"
               class="btn btn-warning text-dark">
                📋 Rapport complet Excel
            </a>

            <a href="{{ route('rapports.export.pdf') }}?date_debut={{ $dateDebut }}&date_fin={{ $dateFin }}"
               target="_blank" class="btn btn-danger">
                🧾 Rapport PDF
            </a>

        </div>
        <p class="text-muted small mt-2 mb-0">
            Les exports Excel incluent les données filtrées par la période sélectionnée ci-dessus.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('ventesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ventesParJour->pluck('date')) !!},
            datasets: [{
                label: 'Ventes (FCFA)',
                data: {!! json_encode($ventesParJour->pluck('total')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: '#3b82f6',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA'
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => new Intl.NumberFormat('fr-FR').format(val) + ' F'
                    }
                }
            }
        }
    });
});
</script>

@endsection

@extends('layouts.app')

@section('content')

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">📊 Dashboard</h2>
    <span class="text-muted small">{{ now()->format('d/m/Y') }}</span>
</div>

{{-- ═══════════════════════════════════════
     LIGNE 1 : KPIs principaux
═══════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">💰</div>
                <div>
                    <div class="text-muted small">Ventes du jour</div>
                    <div class="fw-bold fs-5">{{ number_format($ventesAujourdhui, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">📈</div>
                <div>
                    <div class="text-muted small">Total ventes</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($totalVentes, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">🛍️</div>
                <div>
                    <div class="text-muted small">Total achats</div>
                    <div class="fw-bold fs-5 text-primary">{{ number_format($totalAchats, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">📊</div>
                <div>
                    <div class="text-muted small">Bénéfice sur ventes</div>
                    <div class="fw-bold fs-5 {{ $benefice >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($benefice, 0, ',', ' ') }} FCFA
                    </div>
                    @if($totalVentes == 0)
                        <div class="text-muted" style="font-size:.65rem;">Aucune vente encore</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     LIGNE 2 : Stock & Dettes
═══════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">📦</div>
                <div>
                    <div class="text-muted small">Produits</div>
                    <div class="fw-bold fs-5">{{ $nbProduits }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm border-start border-danger border-3">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">⚠️</div>
                <div>
                    <div class="text-muted small">Ruptures de stock</div>
                    <div class="fw-bold fs-5 text-danger">{{ $ruptures }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm border-start border-warning border-3">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">🔴</div>
                <div>
                    <div class="text-muted small">Dettes totales</div>
                    <div class="fw-bold fs-5 text-warning">{{ number_format($totalDettes, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">👥</div>
                <div>
                    <div class="text-muted small">Clients débiteurs</div>
                    <div class="fw-bold fs-5">{{ $clientsDebiteurs }}</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     LIGNE 3 : Graphique + Top débiteurs
     CORRIGÉ : le canvas était en dehors du row
═══════════════════════════════════════ --}}
<div class="row g-3">

    {{-- Graphique ventes par jour --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">📈 Ventes des 30 derniers jours</h5>
                <canvas id="ventesChart" height="110"></canvas>
            </div>
        </div>
    </div>

    {{-- Top débiteurs --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">🏆 Top débiteurs</h5>

                @forelse($topDebiteurs as $c)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded"
                     style="background:#fef9f0;">
                    <span class="fw-semibold">{{ $c->nom }}</span>
                    <span class="badge bg-danger">{{ number_format($c->solde, 0, ',', ' ') }} FCFA</span>
                </div>
                @empty
                <p class="text-muted text-center mt-4">Aucun débiteur 🎉</p>
                @endforelse

                @if($topDebiteurs->count())
                <a href="{{ route('clients.debiteurs') }}" class="btn btn-outline-danger btn-sm w-100 mt-3">
                    Voir tous les débiteurs →
                </a>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     SCRIPT GRAPHIQUE
═══════════════════════════════════════ --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('ventesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Ventes (FCFA)',
                data: {!! json_encode($data) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4,  // courbe lisse
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
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { size: 11 },
                        callback: val => new Intl.NumberFormat('fr-FR').format(val) + ' F'
                    }
                }
            }
        }
    });

});
</script>

@endsection
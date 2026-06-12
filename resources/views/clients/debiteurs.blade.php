@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">🔴 Clients débiteurs</h2>
    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Tous les clients</a>
</div>

{{-- Résumé global --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">👥</div>
                <div>
                    <div class="text-muted small">Débiteurs</div>
                    <div class="fw-bold fs-4">{{ $clients->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card border-0 shadow-sm border-start border-danger border-3 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">💸</div>
                <div>
                    <div class="text-muted small">Total des dettes</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($totalDettes, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">📊</div>
                <div>
                    <div class="text-muted small">Dette moyenne</div>
                    <div class="fw-bold fs-5">
                        {{ $clients->count() ? number_format($totalDettes / $clients->count(), 0, ',', ' ') : 0 }} FCFA
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($clients->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div style="font-size:3rem;">🎉</div>
            <h5 class="mt-2 fw-bold">Aucun débiteur !</h5>
            <p class="text-muted">Tous les clients sont à jour dans leurs paiements.</p>
        </div>
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Dette</th>
                        <th>Niveau</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $i => $client)
                    @php
                        $pct = $totalDettes > 0 ? ($client->solde / $totalDettes) * 100 : 0;
                        if ($client->solde >= 500000)      { $niveau = ['label'=>'Critique',  'class'=>'bg-danger']; }
                        elseif ($client->solde >= 100000)  { $niveau = ['label'=>'Élevé',     'class'=>'bg-warning text-dark']; }
                        else                               { $niveau = ['label'=>'Modéré',    'class'=>'bg-info text-dark']; }
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $client->nom }}</div>
                            @if($client->adresse)
                                <small class="text-muted">{{ $client->adresse }}</small>
                            @endif
                        </td>
                        <td>{{ $client->telephone ?? '—' }}</td>
                        <td>
                            <div class="fw-bold text-danger">
                                {{ number_format($client->solde, 0, ',', ' ') }} FCFA
                            </div>
                            @php $pctLimite = min(100, ($client->solde / 10000000) * 100); @endphp
                            <div class="progress mt-1" style="height:5px; width:90px;"
                                 title="{{ number_format($pctLimite,1) }}% de la limite">
                                <div class="progress-bar {{ $pctLimite >= 90 ? 'bg-danger' : ($pctLimite >= 70 ? 'bg-warning' : 'bg-success') }}"
                                     style="width:{{ $pctLimite }}%"></div>
                            </div>
                            <small class="text-muted" style="font-size:.65rem;">
                                {{ number_format($pctLimite, 0) }}% du plafond
                            </small>
                        </td>
                        <td>
                            @if($client->solde >= 10000000)
                                <span class="badge bg-dark">🔒 Bloqué</span>
                            @else
                                <span class="badge {{ $niveau['class'] }}">{{ $niveau['label'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('clients.payer.form', $client->id) }}"
                                   class="btn btn-success btn-sm" title="Enregistrer un paiement">
                                    💵 Payer
                                </a>
                                <a href="{{ route('clients.show', $client->id) }}"
                                   class="btn btn-outline-primary btn-sm" title="Voir l'historique">
                                    📜 Historique
                                </a>
                                <a href="{{ route('clients.historique.pdf', $client->id) }}"
                                   target="_blank" class="btn btn-outline-danger btn-sm" title="PDF">
                                    🧾
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="fw-bold text-end">TOTAL</td>
                        <td class="fw-bold text-danger">{{ number_format($totalDettes, 0, ',', ' ') }} FCFA</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

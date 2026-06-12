@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">🚚 Fournisseurs</h2>
    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-sm">➕ Ajouter</a>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">🚚</div>
                <div>
                    <div class="text-muted small">Fournisseurs</div>
                    <div class="fw-bold fs-4">{{ $fournisseurs->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">💰</div>
                <div>
                    <div class="text-muted small">Total dépensé</div>
                    <div class="fw-bold fs-5 text-primary">{{ number_format($totalDepenseGlobal, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">🛍️</div>
                <div>
                    <div class="text-muted small">Total commandes</div>
                    <div class="fw-bold fs-4">{{ $fournisseurs->sum('nb_achats') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($fournisseurs->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div style="font-size:3rem;">🚚</div>
            <h5 class="mt-2 fw-bold">Aucun fournisseur</h5>
            <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary mt-2">➕ Ajouter un fournisseur</a>
        </div>
    </div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th class="d-mobile-none">Téléphone</th>
                        <th class="d-mobile-none">Adresse</th>
                        <th class="text-center">Commandes</th>
                        <th class="text-end">Total dépensé</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fournisseurs as $f)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $f->nom }}{{ $f->prenom ? ' '.$f->prenom : '' }}</div>
                        </td>
                        <td class="d-mobile-none">{{ $f->telephone ?? '—' }}</td>
                        <td class="d-mobile-none">
                            <span class="text-muted small">{{ $f->adresse ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            @if($f->nb_achats > 0)
                                <span class="badge bg-primary">{{ $f->nb_achats }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">
                            @if($f->total_depense > 0)
                                <span class="text-primary">{{ number_format($f->total_depense, 0, ',', ' ') }} FCFA</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('fournisseurs.edit', $f->id) }}"
                                   class="btn btn-warning btn-sm" title="Modifier">✏️</a>
                                <form action="{{ route('fournisseurs.destroy', $f->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer « {{ $f->nom }} » ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Supprimer">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if($fournisseurs->count() > 1)
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="fw-bold text-end d-mobile-none">TOTAL</td>
                        <td colspan="1" class="fw-bold text-end d-md-none">TOTAL</td>
                        <td class="text-center fw-bold">{{ $fournisseurs->sum('nb_achats') }}</td>
                        <td class="text-end fw-bold text-primary">
                            {{ number_format($totalDepenseGlobal, 0, ',', ' ') }} FCFA
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

@endsection

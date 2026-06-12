@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">👥 Clients</h2>
    <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">➕ Nouveau client</a>
</div>

{{-- Barre de recherche --}}
<form method="GET" action="{{ route('clients.index') }}" class="mb-4">
    <div class="input-group" style="max-width:420px;">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="🔍 Nom, téléphone..."
               value="{{ $search ?? '' }}" autocomplete="off">
        @if($search)
            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm" title="Effacer">✕</a>
        @endif
        <button class="btn btn-outline-primary btn-sm">Rechercher</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th class="d-mobile-none">Type</th>
                        <th class="d-mobile-none">Crédit autorisé</th>
                        <th>Dette</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td class="fw-semibold">{{ $client->nom }}</td>
                        <td>{{ $client->telephone ?? '—' }}</td>
                        <td class="d-mobile-none">
                            <span class="badge {{ $client->est_particulier ? 'bg-primary' : 'bg-secondary' }}">
                                {{ $client->est_particulier ? 'Particulier' : 'Simple' }}
                            </span>
                        </td>
                        <td class="d-mobile-none">
                            @if($client->a_credit)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-light text-dark border">Non</span>
                            @endif
                        </td>
                        <td>
                            @if($client->solde > 0)
                                <span class="fw-bold text-danger">{{ number_format($client->solde, 0, ',', ' ') }} FCFA</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('clients.show', $client->id) }}"
                                   class="btn btn-outline-primary btn-sm" title="Voir">👁️</a>
                                <a href="{{ route('clients.edit', $client->id) }}"
                                   class="btn btn-warning btn-sm" title="Modifier">✏️</a>
                                @if($client->solde > 0)
                                    <a href="{{ route('clients.payer.form', $client->id) }}"
                                       class="btn btn-success btn-sm" title="Enregistrer un paiement">💵</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            @if($search)
                                Aucun client trouvé pour « {{ $search }} »
                            @else
                                Aucun client enregistré
                            @endif
                        </td>
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
        {{ $clients->total() }} client(s)
        @if($search) pour « {{ $search }} » @endif
        — page {{ $clients->currentPage() }}/{{ $clients->lastPage() }}
    </small>
    {{ $clients->links() }}
</div>

@endsection

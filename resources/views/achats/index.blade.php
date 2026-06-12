@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">🛍️ Achats fournisseurs</h2>
    <a href="{{ route('achats.create') }}" class="btn btn-primary btn-sm">➕ Nouveau bon d'achat</a>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">💰</div>
                <div>
                    <div class="text-muted small">Total achats</div>
                    <div class="fw-bold fs-5 text-primary">{{ number_format($totalGlobal, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">📅</div>
                <div>
                    <div class="text-muted small">Ce mois-ci</div>
                    <div class="fw-bold fs-5">{{ number_format($achatsMois, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1">🚚</div>
                <div>
                    <div class="text-muted small">Fournisseurs</div>
                    <div class="fw-bold fs-5">{{ $nbFournisseurs }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtres par catégorie --}}
@if($categories->count())
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="{{ route('achats.index', array_filter(['search'=>$search,'fournisseur_id'=>$fournId,'date_debut'=>$dateDebut,'date_fin'=>$dateFin])) }}"
       class="btn btn-sm {{ !$categorieId ? 'btn-dark' : 'btn-outline-secondary' }}">
        Toutes catégories
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('achats.index', array_filter(['search'=>$search,'fournisseur_id'=>$fournId,'date_debut'=>$dateDebut,'date_fin'=>$dateFin,'categorie_id'=>$cat->id])) }}"
       class="btn btn-sm {{ $categorieId == $cat->id ? 'text-white' : '' }}"
       style="{{ $categorieId == $cat->id
           ? 'background:'.$cat->couleur.';border-color:'.$cat->couleur.';'
           : 'border-color:'.$cat->couleur.';color:'.$cat->couleur.';' }}">
        {{ $cat->nom }}
    </a>
    @endforeach
</div>
@endif

{{-- Filtres --}}
<form method="GET" action="{{ route('achats.index') }}" class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Produit</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nom du produit..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Fournisseur</label>
                <select name="fournisseur_id" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($fournisseurs as $f)
                        <option value="{{ $f->id }}" {{ ($fournId ?? '') == $f->id ? 'selected' : '' }}>
                            {{ $f->nom }} {{ $f->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Du</label>
                <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ $dateDebut ?? '' }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Au</label>
                <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ $dateFin ?? '' }}">
            </div>
            @if($categorieId)
                <input type="hidden" name="categorie_id" value="{{ $categorieId }}">
            @endif
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-grow-1">🔍 Filtrer</button>
                @if($search || $dateDebut || $dateFin || $fournId || $categorieId)
                    <a href="{{ route('achats.index') }}" class="btn btn-outline-secondary btn-sm">✕</a>
                @endif
            </div>
        </div>
    </div>
</form>

@if($search || $dateDebut || $dateFin || $fournId || $categorieId)
<div class="alert alert-info py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
    <span><strong>{{ $bons->total() }}</strong> bon(s) trouvé(s)</span>
    <span class="fw-bold">Total : {{ number_format($totalFiltré, 0, ',', ' ') }} FCFA</span>
</div>
@endif

{{-- Liste des bons d'achat --}}
@forelse($bons as $bon)
<div class="card border-0 shadow-sm mb-4" style="border-radius:14px; overflow:hidden;">

    {{-- En-tête du bon --}}
    <div style="background:linear-gradient(135deg,#0f172a,#1e3a5f); color:white; padding:14px 20px;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <div class="fw-bold fs-6 mb-1">
                    🚚 {{ $bon->fournisseur->nom ?? '—' }}
                    @if($bon->fournisseur->prenom) {{ $bon->fournisseur->prenom }} @endif
                </div>
                <div class="d-flex gap-3 flex-wrap" style="font-size:.78rem; color:#94a3b8;">
                    <span>📅 {{ \Carbon\Carbon::parse($bon->date_achat)->format('d/m/Y') }}</span>
                    <span>📦 {{ $bon->achats->count() }} produit(s)</span>
                    @if($bon->reference)
                        <span>🔖 Réf : {{ $bon->reference }}</span>
                    @endif
                </div>
            </div>
            <div class="text-end">
                <div style="font-size:.7rem; color:#94a3b8;">TOTAL BON</div>
                @php $totalReel = $bon->achats->sum(fn($a) => $a->prix * $a->quantite); @endphp
            <div class="fw-bold fs-5" style="color:#60a5fa;">
                    {{ number_format($totalReel, 0, ',', ' ') }} FCFA
                </div>
            </div>
        </div>
    </div>

    {{-- Lignes produits --}}
    <div class="p-3 d-flex flex-column gap-2" style="background:#f8fafc;">
        @foreach($bon->achats as $i => $achat)
        @php
            $cat    = $achat->produit?->categorie;
            $couleur = $cat?->couleur ?? '#64748b';
        @endphp
        <div class="bg-white rounded-3 d-flex align-items-center gap-3 px-3 py-2 shadow-sm"
             style="border-left: 4px solid {{ $couleur }};">

            {{-- Numéro de ligne --}}
            <div class="text-muted fw-bold" style="font-size:.7rem; width:18px; flex-shrink:0;">
                {{ $i + 1 }}
            </div>

            {{-- Produit + catégorie --}}
            <div style="flex:1; min-width:0;">
                <div class="fw-semibold small text-truncate">
                    {{ $achat->produit->nom ?? '—' }}
                </div>
                @if($cat)
                    <span class="badge" style="background:{{ $couleur }}; font-size:.6rem;">
                        {{ $cat->nom }}
                    </span>
                @else
                    <span class="text-muted" style="font-size:.65rem;">Sans catégorie</span>
                @endif
            </div>

            {{-- Quantité --}}
            <div class="text-center" style="min-width:52px;">
                <div class="fw-bold">{{ $achat->quantite }}</div>
                <div class="text-muted" style="font-size:.65rem;">unité(s)</div>
            </div>

            {{-- Prix unitaire --}}
            <div class="text-end d-none d-md-block" style="min-width:90px;">
                <div class="text-muted small">{{ number_format($achat->prix, 0, ',', ' ') }} F</div>
                <div class="text-muted" style="font-size:.62rem;">/ unité</div>
            </div>

            {{-- Sous-total --}}
            <div class="text-end" style="min-width:90px;">
                <div class="fw-bold text-primary small">
                    {{ number_format($achat->total, 0, ',', ' ') }} FCFA
                </div>
                <div class="text-muted" style="font-size:.62rem;">sous-total</div>
            </div>

        </div>
        @endforeach
    </div>

</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <div style="font-size:2.5rem;">🛍️</div>
        <p class="mt-2">Aucun bon d'achat enregistré</p>
        <a href="{{ route('achats.create') }}" class="btn btn-primary btn-sm">➕ Premier bon d'achat</a>
    </div>
</div>
@endforelse

{{-- Pagination --}}
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <small class="text-muted">
        {{ $bons->total() }} bon(s) — page {{ $bons->currentPage() }}/{{ $bons->lastPage() }}
    </small>
    {{ $bons->links() }}
</div>

@endsection

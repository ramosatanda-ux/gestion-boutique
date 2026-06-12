@php $stockBas = $produits->filter(fn($p) => $p->quantite <= ($p->stock_minimum ?? 5)); @endphp

@if($stockBas->count() > 0 && !request()->ajax())
    <div class="alert alert-warning mb-4">
        ⚠️ <strong>{{ $stockBas->count() }} produit(s) en stock bas</strong> — pensez à réapprovisionner.
    </div>
@endif

@if($produits->isEmpty())
    <div class="text-center text-muted py-5">
        <div style="font-size:3rem;">🔍</div>
        <p class="mt-2 fw-semibold">Aucun produit trouvé
            @if($search) pour « {{ $search }} » @endif
        </p>
        @if($search)
            <button onclick="clearSearch()" class="btn btn-outline-secondary btn-sm">✕ Effacer la recherche</button>
        @else
            <a href="{{ route('produits.create') }}" class="btn btn-primary btn-sm">➕ Ajouter un produit</a>
        @endif
    </div>
@else
<div class="row g-3">
    @foreach($produits as $produit)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100 border-0 shadow-sm product-card {{ $produit->stockBas() ? 'border-warning border-2' : '' }}"
             style="border-radius:14px; overflow:hidden;">

            <div style="height:160px; background:#f8fafc; overflow:hidden; position:relative;">
                @if($produit->image)
                    <img src="{{ Storage::url($produit->image) }}"
                         alt="{{ $produit->nom }}"
                         style="width:100%; height:100%; object-fit:cover;">
                @else
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:3.5rem; opacity:.3;">📦</span>
                    </div>
                @endif

                @if($produit->quantite == 0)
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Rupture</span>
                @elseif($produit->stockBas())
                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Stock bas</span>
                @endif
            </div>

            <div class="card-body p-3 d-flex flex-column">
                {{-- Badge catégorie --}}
                @if($produit->categorie)
                    <span class="badge mb-1 align-self-start" style="background:{{ $produit->categorie->couleur }}; font-size:.65rem;">
                        {{ $produit->categorie->nom }}
                    </span>
                @endif

                <div class="fw-semibold mb-1" style="font-size:.92rem; line-height:1.3;">
                    {{ $produit->nom }}
                </div>

                @if($produit->description)
                    <div class="text-muted mb-2" style="font-size:.75rem; line-height:1.3;">
                        {{ Str::limit($produit->description, 45) }}
                    </div>
                @endif

                <div class="mt-auto">
                    <div class="fw-bold text-primary mb-1" style="font-size:1rem;">
                        {{ number_format($produit->prix, 0, ',', ' ') }} FCFA
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Stock : <strong>{{ $produit->quantite }}</strong></small>
                        @if($produit->code_barre)
                            <small class="text-muted" style="font-size:.65rem;">{{ $produit->code_barre }}</small>
                        @endif
                    </div>

                    <div class="d-flex gap-1">
                        <a href="{{ route('produits.edit', $produit->id) }}"
                           class="btn btn-warning btn-sm flex-grow-1" title="Modifier">✏️</a>

                        @if(!$produit->code_barre)
                            <form action="{{ route('codebarre.generer', $produit->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-secondary btn-sm" title="Générer code-barres">🔢</button>
                            </form>
                        @else
                            <a href="{{ route('codebarre.imprimer') }}?ids={{ $produit->id }}"
                               target="_blank" class="btn btn-info btn-sm text-white" title="Imprimer étiquette">🖨️</a>
                        @endif

                        <form action="{{ route('produits.destroy', $produit->id) }}" method="POST"
                              onsubmit="return confirm('Supprimer « {{ $produit->nom }} » ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" title="Supprimer">🗑️</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
    <small class="text-muted">
        {{ $produits->total() }} produit(s)
        @if($search) pour « <strong>{{ $search }}</strong> » @endif
        — page {{ $produits->currentPage() }}/{{ $produits->lastPage() }}
    </small>
    {{ $produits->links() }}
</div>
@endif

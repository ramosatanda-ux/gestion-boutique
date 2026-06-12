@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">📦 Produits</h2>
    <div class="d-flex gap-2 flex-wrap">
        <form action="{{ route('codebarre.generer-tous') }}" method="POST">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">🔢 Codes manquants</button>
        </form>
        <a href="{{ route('codebarre.imprimer') }}" target="_blank" class="btn btn-outline-danger btn-sm">
            🖨️ Imprimer tous
        </a>
        <a href="{{ route('produits.create') }}" class="btn btn-primary btn-sm">➕ Ajouter</a>
    </div>
</div>

{{-- Filtres par catégorie --}}
@if($categories->count())
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="{{ route('produits.index', array_filter(['search' => $search])) }}"
       class="btn btn-sm {{ !$categorieId ? 'btn-dark' : 'btn-outline-secondary' }}">
        Tous <span class="badge bg-secondary ms-1">{{ $produits->total() }}</span>
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('produits.index', array_filter(['search' => $search, 'categorie_id' => $cat->id])) }}"
       class="btn btn-sm {{ $categorieId == $cat->id ? 'text-white' : '' }}"
       style="{{ $categorieId == $cat->id
           ? 'background:'.$cat->couleur.'; border-color:'.$cat->couleur.';'
           : 'border-color:'.$cat->couleur.'; color:'.$cat->couleur.';' }}">
        {{ $cat->nom }}
        <span class="badge ms-1" style="background:{{ $categorieId == $cat->id ? 'rgba(255,255,255,0.3)' : $cat->couleur }}; color:{{ $categorieId == $cat->id ? 'white' : 'white' }}">
            {{ $cat->produits_count }}
        </span>
    </a>
    @endforeach
    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-primary ms-2">
        ⚙️ Gérer
    </a>
</div>
@endif

{{-- Barre de recherche instantanée --}}
<div class="mb-4" style="max-width:460px;">
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;">
            <span id="search-icon">🔍</span>
        </span>
        <input type="text" id="search-input"
               class="form-control border-start-0 ps-0"
               placeholder="Nom, code-barres, description..."
               value="{{ $search ?? '' }}"
               autocomplete="off"
               style="border-radius:0; font-size:.95rem;">
        <button id="clear-btn" class="btn btn-outline-secondary border-start-0"
                style="display:{{ ($search ?? '') ? 'block' : 'none' }}; border-radius:0 10px 10px 0;"
                onclick="clearSearch()" title="Effacer">✕</button>
    </div>
    <div id="search-info" class="text-muted small mt-1" style="min-height:18px;">
        @if($search) Résultats pour « {{ $search }} » @endif
    </div>
</div>

{{-- Zone de résultats (rechargée en AJAX) --}}
<div id="produits-results">
    @include('produits._grille', ['produits' => $produits, 'search' => $search])
</div>

<style>
.product-card { transition: transform .2s, box-shadow .2s; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important; }

#search-input:focus { box-shadow: none; border-color: #3b82f6; }
.input-group:focus-within { box-shadow: 0 0 0 3px rgba(59,130,246,0.15); border-radius: 10px; }

/* Spinner de chargement */
.search-spinner {
    display: inline-block;
    width: 16px; height: 16px;
    border: 2px solid #e2e8f0;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin .6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Résultats : fondu lors du chargement */
#produits-results.loading { opacity: .4; pointer-events: none; transition: opacity .15s; }
</style>

<script>
const SEARCH_URL  = '{{ route('produits.index') }}';
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
const CAT_ID      = '{{ $categorieId ?? '' }}';
let   searchTimer = null;

const input      = document.getElementById('search-input');
const clearBtn   = document.getElementById('clear-btn');
const searchIcon = document.getElementById('search-icon');
const searchInfo = document.getElementById('search-info');
const results    = document.getElementById('produits-results');

input.addEventListener('input', () => {
    clearTimeout(searchTimer);
    const q = input.value.trim();

    clearBtn.style.display = q ? 'block' : 'none';

    // Spinner pendant l'attente
    searchIcon.innerHTML = q
        ? '<span class="search-spinner"></span>'
        : '🔍';

    searchTimer = setTimeout(() => rechercherProduits(q), 300);
});

async function rechercherProduits(q) {
    results.classList.add('loading');

    try {
        const params = new URLSearchParams();
        if (q)      params.set('search', q);
        if (CAT_ID) params.set('categorie_id', CAT_ID);
        const url = SEARCH_URL + (params.toString() ? '?' + params.toString() : '');
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            }
        });

        if (!res.ok) throw new Error();

        const html = await res.text();
        results.innerHTML = html;

        // Mettre à jour l'URL sans recharger la page
        history.replaceState(null, '', url);

        // Info sous la barre
        searchInfo.textContent = q ? `Résultats pour « ${q} »` : '';

    } catch (e) {
        // En cas d'erreur, fallback vers rechargement classique
        window.location = q ? `${SEARCH_URL}?search=${encodeURIComponent(q)}` : SEARCH_URL;
    } finally {
        results.classList.remove('loading');
        searchIcon.innerHTML = '🔍';
    }
}

function clearSearch() {
    input.value = '';
    clearBtn.style.display = 'none';
    searchInfo.textContent = '';
    rechercherProduits('');
    input.focus();
}

// Empêcher la soumission du formulaire par Entrée (on gère via JS)
input.addEventListener('keydown', e => {
    if (e.key === 'Enter') e.preventDefault();
});
</script>

@endsection
@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">✏️ Modifier un produit</h2>
    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">⬅ Retour</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px;">
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('produits.update', $produit->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Photo du produit --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Photo du produit</label>
                <div class="d-flex align-items-center gap-3">
                    <div id="preview-zone" style="
                        width:90px; height:90px; border-radius:12px;
                        background:#f1f5f9; border:2px dashed #cbd5e1;
                        display:flex; align-items:center; justify-content:center;
                        overflow:hidden; flex-shrink:0;">
                        @if($produit->image)
                            <img id="preview-img" src="{{ Storage::url($produit->image) }}"
                                 alt="" style="width:100%; height:100%; object-fit:cover;">
                            <span id="preview-icon" style="display:none; font-size:2rem;">📷</span>
                        @else
                            <span id="preview-icon" style="font-size:2rem;">📷</span>
                            <img id="preview-img" src="" alt="" style="display:none; width:100%; height:100%; object-fit:cover;">
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="image" id="image-input"
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewImage(this)">
                        <div class="form-text">JPG, PNG ou WEBP · max 2 Mo</div>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        @if($produit->image)
                        <div class="form-check mt-2">
                            <input type="checkbox" name="supprimer_image" id="supprimer_image"
                                   class="form-check-input" value="1"
                                   onchange="toggleSupprimerImage(this)">
                            <label class="form-check-label text-danger small" for="supprimer_image">
                                Supprimer la photo actuelle
                            </label>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Catégorie</label>
                <div class="d-flex gap-2">
                    <select name="categorie_id" class="form-select">
                        <option value="">— Sans catégorie —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('categorie_id', $produit->categorie_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary" title="Gérer les catégories" target="_blank">⚙️</a>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nom du produit <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                       value="{{ old('nom', $produit->nom) }}" required>
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <input type="text" name="description" class="form-control"
                       value="{{ old('description', $produit->description) }}">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" name="prix" class="form-control @error('prix') is-invalid @enderror"
                           value="{{ old('prix', $produit->prix) }}" min="0" required>
                    @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Quantité en stock <span class="text-danger">*</span></label>
                    <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite', $produit->quantite) }}" min="0" required>
                    @error('quantite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Stock minimum (alerte)</label>
                <input type="number" name="stock_minimum" class="form-control"
                       value="{{ old('stock_minimum', $produit->stock_minimum ?? 5) }}" min="0">
                <div class="form-text">Alerte quand le stock descend sous ce seuil.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">💾 Enregistrer</button>
                <a href="{{ route('produits.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const img  = document.getElementById('preview-img');
    const icon = document.getElementById('preview-icon');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src           = e.target.result;
            img.style.display = 'block';
            icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleSupprimerImage(checkbox) {
    const img  = document.getElementById('preview-img');
    const icon = document.getElementById('preview-icon');
    if (checkbox.checked) {
        img.style.display  = 'none';
        icon.style.display = 'block';
    } else {
        img.style.display  = 'block';
        icon.style.display = 'none';
    }
}
</script>

@endsection

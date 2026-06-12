@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">🏷️ Catégories de produits</h2>
    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary btn-sm">⬅ Produits</a>
</div>

<div class="row g-3">

    {{-- Formulaire d'ajout --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">➕ Nouvelle catégorie</h5>

                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3">
                        @foreach($errors->all() as $e)<div class="small">{{ $e }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Ex: Alimentation, Électronique…" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Couleur du badge</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="couleur" class="form-control form-control-color"
                                   value="{{ old('couleur', '#3b82f6') }}" style="width:48px; height:38px; padding:2px;">
                            <div class="d-flex gap-1 flex-wrap">
                                @foreach(['#3b82f6','#16a34a','#dc2626','#f59e0b','#8b5cf6','#ec4899','#0891b2','#64748b'] as $c)
                                    <button type="button" onclick="document.querySelector('[name=couleur]').value='{{ $c }}'"
                                            class="border-0 rounded" style="width:22px;height:22px;background:{{ $c }};cursor:pointer;"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">💾 Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Liste des catégories --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if($categories->isEmpty())
                    <div class="text-center text-muted py-5">
                        <div style="font-size:2.5rem;">🏷️</div>
                        <p class="mt-2">Aucune catégorie créée</p>
                    </div>
                @else
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Catégorie</th>
                            <th class="text-center">Produits</th>
                            <th>Modifier</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr>
                            <td>
                                <span class="badge fs-6 px-3 py-2" style="background:{{ $cat->couleur }};">
                                    {{ $cat->nom }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('produits.index', ['categorie_id' => $cat->id]) }}"
                                   class="badge bg-light text-dark border text-decoration-none">
                                    {{ $cat->produits_count }} produit(s)
                                </a>
                            </td>
                            <td>
                                {{-- Édition inline --}}
                                <form method="POST" action="{{ route('categories.update', $cat->id) }}"
                                      class="d-flex gap-2 align-items-center">
                                    @csrf @method('PUT')
                                    <input type="text" name="nom" value="{{ $cat->nom }}"
                                           class="form-control form-control-sm" style="width:140px;" required>
                                    <input type="color" name="couleur" value="{{ $cat->couleur }}"
                                           class="form-control form-control-color form-control-sm"
                                           style="width:36px; height:32px; padding:2px;">
                                    <button class="btn btn-warning btn-sm" title="Enregistrer">💾</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('categories.destroy', $cat->id) }}"
                                      onsubmit="return confirm('Supprimer « {{ $cat->nom }} » ?\nLes produits liés ne seront pas supprimés.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

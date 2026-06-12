<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\VenteItem;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->input('search');
        $categorieId = $request->input('categorie_id');

        $produits = Produit::with('categorie')
            ->when($search, fn($q) => $q->where('nom', 'like', "%{$search}%")
                                        ->orWhere('code_barre', 'like', "%{$search}%")
                                        ->orWhere('description', 'like', "%{$search}%"))
            ->when($categorieId, fn($q) => $q->where('categorie_id', $categorieId))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        $categories = Categorie::withCount('produits')->orderBy('nom')->get();

        if ($request->ajax()) {
            return view('produits._grille', compact('produits', 'search', 'categorieId', 'categories'));
        }

        return view('produits.index', compact('produits', 'search', 'categorieId', 'categories'));
    }

    public function create()
    {
        $categories = Categorie::orderBy('nom')->get();
        return view('produits.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'           => 'required|string|max:255',
            'categorie_id'  => 'nullable|exists:categories,id',
            'prix'          => 'required|numeric|min:0',
            'quantite'      => 'required|integer|min:0',
            'stock_minimum' => 'nullable|integer|min:0',
            'description'   => 'nullable|string|max:500',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['nom', 'categorie_id', 'prix', 'quantite', 'stock_minimum', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        Produit::create($data);

        return redirect()->route('produits.index')
                         ->with('success', 'Produit ajouté avec succès.');
    }

    public function show(int $id)
    {
        $produit = Produit::with('venteItems')->findOrFail($id);
        return view('produits.show', compact('produit'));
    }

    public function edit(int $id)
    {
        $produit    = Produit::findOrFail($id);
        $categories = Categorie::orderBy('nom')->get();
        return view('produits.edit', compact('produit', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nom'           => 'required|string|max:255',
            'categorie_id'  => 'nullable|exists:categories,id',
            'prix'          => 'required|numeric|min:0',
            'quantite'      => 'required|integer|min:0',
            'stock_minimum' => 'nullable|integer|min:0',
            'description'   => 'nullable|string|max:500',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $produit = Produit::findOrFail($id);
        $data    = $request->only(['nom', 'categorie_id', 'prix', 'quantite', 'stock_minimum', 'description']);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        // Permettre de supprimer l'image manuellement
        if ($request->boolean('supprimer_image') && $produit->image) {
            Storage::disk('public')->delete($produit->image);
            $data['image'] = null;
        }

        $produit->update($data);

        return redirect()->route('produits.index')
                         ->with('success', "Produit « {$produit->nom} » modifié.");
    }

    public function destroy(int $id)
    {
        $produit = Produit::findOrFail($id);

        if (VenteItem::where('produit_id', $id)->exists()) {
            return redirect()->route('produits.index')
                             ->with('error', "Impossible de supprimer « {$produit->nom} » — il est référencé dans des ventes.");
        }

        if ($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }

        $nom = $produit->nom;
        $produit->delete();

        return redirect()->route('produits.index')
                         ->with('success', "Produit « {$nom} » supprimé.");
    }
}

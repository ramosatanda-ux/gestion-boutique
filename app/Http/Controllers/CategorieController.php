<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('produits')->orderBy('nom')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'     => 'required|string|max:100|unique:categories,nom',
            'couleur' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Categorie::create($request->only(['nom', 'couleur']));

        return redirect()->route('categories.index')
                         ->with('success', "Catégorie « {$request->nom} » créée.");
    }

    public function update(Request $request, Categorie $category)
    {
        $request->validate([
            'nom'     => 'required|string|max:100|unique:categories,nom,' . $category->id,
            'couleur' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $category->update($request->only(['nom', 'couleur']));

        return redirect()->route('categories.index')
                         ->with('success', "Catégorie « {$category->nom} » modifiée.");
    }

    public function destroy(Categorie $category)
    {
        $nom = $category->nom;
        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', "Catégorie « {$nom} » supprimée.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index()
    {
        $fournisseurs = Fournisseur::with('achats')
            ->orderBy('nom')
            ->get()
            ->map(function ($f) {
                $f->nb_achats     = $f->achats->count();
                $f->total_depense = $f->achats->sum(fn($a) => $a->prix * $a->quantite);
                return $f;
            });

        $totalDepenseGlobal = $fournisseurs->sum('total_depense');

        return view('fournisseurs.index', compact('fournisseurs', 'totalDepenseGlobal'));
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string|max:500',
        ]);

        Fournisseur::create($request->only(['nom', 'prenom', 'telephone', 'adresse']));

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur ajouté.');
    }

    public function edit($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, $id)
    {
        // CORRIGÉ : $request->all() sans validation
        $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string|max:500',
        ]);

        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->update($request->only(['nom', 'prenom', 'telephone', 'adresse']));

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur modifié.');
    }

    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur supprimé.');
    }
}
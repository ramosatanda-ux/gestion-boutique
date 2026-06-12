<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Console\Commands\GenererCodeBarres;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CodeBarreController extends Controller
{
    // Génère un code-barres pour UN produit (appelé depuis la liste produits)
    public function generer(int $id)
    {
        $produit = Produit::findOrFail($id);

        if (empty($produit->code_barre)) {
            $produit->code_barre = GenererCodeBarres::genererEan13($produit->id);
            $produit->save();
        }

        return back()->with('success', "Code-barres généré pour « {$produit->nom} » : {$produit->code_barre}");
    }

    // Génère les codes-barres pour TOUS les produits qui n'en ont pas
    public function genererTous()
    {
        $produits = Produit::whereNull('code_barre')->orWhere('code_barre', '')->get();
        $nb = $produits->count();

        foreach ($produits as $produit) {
            $produit->code_barre = GenererCodeBarres::genererEan13($produit->id);
            $produit->save();
        }

        return back()->with('success', "{$nb} code(s)-barres généré(s) avec succès.");
    }

    // Page d'impression des codes-barres (PDF)
    public function imprimer(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;

        $produits = $ids
            ? Produit::whereIn('id', $ids)->whereNotNull('code_barre')->get()
            : Produit::whereNotNull('code_barre')->where('code_barre', '!=', '')->get();

        if ($produits->isEmpty()) {
            return back()->with('error', 'Aucun produit avec code-barres à imprimer.');
        }

        $pdf = Pdf::loadView('codebarre.imprimer', compact('produits'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('codebarre-produits.pdf');
    }
}

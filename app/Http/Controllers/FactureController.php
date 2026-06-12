<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



  

use App\Models\Facture;
use App\Models\VenteItem;
use App\Models\Produit;

class FactureController extends Controller
{
    
    public function store(Request $request)
    {
        $facture = Facture::create([
            'numero' => 'FAC-' . time(),
            'nom_client' => $request->nom_client,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse
        ]);

        foreach ($request->produits as $produit) {

            $p = Produit::find($produit['id']);

            $total = $p->prix * $produit['quantite'];

            VenteItem::create([
                'facture_id' => $facture->id,
                'produit_id' => $p->id,
                'quantite' => $produit['quantite'],
                'prix' => $p->prix,
                'total' => $total
            ]);
        }

        return redirect()->back()->with('success', 'Facture créée');
    }
}


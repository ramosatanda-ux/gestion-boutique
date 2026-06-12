<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\VenteItem;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller
{
    // Historique des ventes de l'utilisateur connecté
    public function historique()
    {
        $ventes = Vente::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        $totalAujourdhui = Vente::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->sum('total');

        $nbAujourdhui = Vente::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->count();

        return view('pos.historique', compact('ventes', 'totalAujourdhui', 'nbAujourdhui'));
    }

    // Interface caisse principale
    public function index()
    {
        $clients = Client::orderBy('nom')->get();
        return view('pos.index', compact('clients'));
    }

    // Recherche produit par code-barres OU nom (appelé en AJAX)
    public function rechercherProduit(Request $request)
    {
        $query = trim($request->query('q', ''));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $produits = Produit::where(function ($q) use ($query) {
                // Recherche exacte par code-barres en priorité
                $q->where('code_barre', $query)
                  // Ou recherche partielle par nom
                  ->orWhere('nom', 'like', "%{$query}%");
            })
            ->where('quantite', '>', 0) // seulement les produits en stock
            ->limit(10)
            ->get(['id', 'nom', 'code_barre', 'prix', 'quantite', 'description']);

        return response()->json($produits);
    }

    // Recherche par code-barres exact (scan rapide)
    public function scannerCodeBarre(Request $request)
    {
        $request->validate(['code_barre' => 'required|string']);

        $code    = trim($request->code_barre);
        $produit = Produit::where('code_barre', $code)->first();

        if (!$produit) {
            return response()->json(['error' => "Produit introuvable pour le code : {$code}"], 404);
        }

        if ($produit->quantite <= 0) {
            return response()->json(['error' => "Stock épuisé pour : {$produit->nom}"], 400);
        }

        return response()->json($produit);
    }

    // Enregistrement de la vente depuis le POS
    public function enregistrerVente(Request $request)
    {
        $request->validate([
            'panier'        => 'required|array|min:1',
            'panier.*.id'   => 'required|exists:produits,id',
            'panier.*.qte'  => 'required|integer|min:1',
            'montant_recu'  => 'nullable|numeric|min:0',
        ]);

        $client    = $request->client_id ? Client::find($request->client_id) : null;
        $estCredit = (bool) $request->est_credit;
        $limite    = 10_000_000;

        if ($estCredit) {
            if (!$client || !$client->est_particulier || !$client->a_credit) {
                return response()->json(['error' => 'Client non autorisé au crédit.'], 422);
            }
            if ($client->solde >= $limite) {
                return response()->json(['error' => 'Client bloqué — dette trop élevée.'], 422);
            }
        }

        // Vérification stock + calcul total AVANT toute écriture
        $lignes      = [];
        $totalGlobal = 0;

        foreach ($request->panier as $item) {
            $produit  = Produit::findOrFail($item['id']);
            $quantite = (int) $item['qte'];

            if ($quantite > $produit->quantite) {
                return response()->json([
                    'error' => "Stock insuffisant pour « {$produit->nom} » (disponible : {$produit->quantite})"
                ], 422);
            }

            $lignes[] = [
                'produit'  => $produit,
                'quantite' => $quantite,
                'prix'     => $produit->prix,
                'total'    => $produit->prix * $quantite,
            ];
            $totalGlobal += $produit->prix * $quantite;
        }

        if ($estCredit && $client && ($client->solde + $totalGlobal) > $limite) {
            return response()->json(['error' => 'Dette maximale dépassée.'], 422);
        }

        $montantRecu = (float) ($request->montant_recu ?? $totalGlobal);
        $monnaie     = $montantRecu - $totalGlobal;

        $vente = DB::transaction(function () use ($lignes, $totalGlobal, $client, $estCredit, $request) {
            $vente = Vente::create([
                'numero'     => 'POS-' . strtoupper(uniqid()),
                'nom_client' => $client ? $client->nom : ($request->nom_client ?? 'Client comptoir'),
                'telephone'  => $client?->telephone ?? $request->telephone,
                'adresse'    => $client?->adresse,
                'client_id'  => $client?->id,
                'user_id'    => auth()->id(),
                'est_credit' => $estCredit,
                'total'      => $totalGlobal,
            ]);

            foreach ($lignes as $ligne) {
                VenteItem::create([
                    'vente_id'   => $vente->id,
                    'produit_id' => $ligne['produit']->id,
                    'quantite'   => $ligne['quantite'],
                    'prix'       => $ligne['prix'],
                    'total'      => $ligne['total'],
                ]);
                $ligne['produit']->decrement('quantite', $ligne['quantite']);
            }

            if ($estCredit && $client) {
                $client->increment('solde', $totalGlobal);
            }

            return $vente;
        });

        return response()->json([
            'success'     => true,
            'vente_id'    => $vente->id,
            'numero'      => $vente->numero,
            'total'       => $totalGlobal,
            'monnaie'     => max(0, $monnaie),
            'facture_url' => route('ventes.facture', $vente->id),
        ]);
    }

    // Fallback serveur si BarcodeDetector est indisponible côté client (Safari/iPhone)
    public function decoderImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['code' => null]);
        }

        // Le décodage de code-barres nécessite une librairie native (ex: zxing-php).
        // Sans celle-ci, on indique au front de réessayer en se rapprochant.
        return response()->json(['code' => null]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Vente;
use App\Models\Client;
use App\Models\Achat;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // La caissière va directement au POS
        if (auth()->user()->role === 'caissiere') {
            return redirect()->route('pos.index');
        }
        // CORRIGÉ : $totalDettes, $clientsDebiteurs et $totalAchats étaient chacun calculés 2 fois

        $totalVentes      = Vente::sum('total');
        $nbProduits       = Produit::count();
        $ruptures         = Produit::where('quantite', 0)->count();
        $produitsBas      = Produit::whereColumn('quantite', '<=', 'stock_minimum')->count();
        $totalDettes      = Client::sum('solde');
        $clientsDebiteurs = Client::where('solde', '>', 0)->count();
        $topDebiteurs     = Client::orderByDesc('solde')->take(5)->get();

        // Total de tous les achats (investissement stock)
        $totalAchats = Achat::sum(DB::raw('prix * quantite'));

        // Coût réel des marchandises vendues
        // = quantité vendue × prix moyen d'achat par produit
        $coutVentes = DB::table('vente_items as vi')
            ->join(
                DB::raw('(SELECT produit_id, AVG(prix) as prix_moyen FROM achats GROUP BY produit_id) as a'),
                'a.produit_id', '=', 'vi.produit_id'
            )
            ->sum(DB::raw('vi.quantite * a.prix_moyen'));

        // Bénéfice = ce qu'on a encaissé - ce qu'ont coûté les articles vendus
        // Si aucune vente : 0 (et non -totalAchats)
        $benefice = $totalVentes - $coutVentes;

        // Ventes par jour (7 derniers jours) pour le graphique
        $ventesParJour = DB::table('ventes')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $ventesParJour->pluck('date');
        $data   = $ventesParJour->pluck('total');

        // Ventes du jour
        $ventesAujourdhui = Vente::whereDate('created_at', today())->sum('total'); // AJOUT

        return view('dashboard', compact(
            'totalVentes',
            'nbProduits',
            'ruptures',
            'produitsBas',
            'totalDettes',
            'clientsDebiteurs',
            'topDebiteurs',
            'benefice',
            'totalAchats',
            'ventesAujourdhui',
            'labels',
            'data'
        ));
    }
}
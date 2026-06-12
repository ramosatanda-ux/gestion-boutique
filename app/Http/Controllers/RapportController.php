<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\Achat;
use App\Models\Client;
use App\Exports\VentesExport;
use App\Exports\StocksExport;
use App\Exports\RapportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    // Page principale des rapports
    public function index(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->startOfMonth()->format('Y-m-d');
        $dateFin   = $request->date_fin   ?? now()->endOfMonth()->format('Y-m-d');

        $ventes       = Vente::whereBetween('created_at', [$dateDebut, $dateFin])->get();
        $totalVentes  = $ventes->sum('total');
        $totalCredit  = $ventes->where('est_credit', true)->sum('total');
        $nbVentes     = $ventes->count();

        // Investissement stock sur la période
        $totalAchats = Achat::whereBetween('date_achat', [$dateDebut, $dateFin])
                            ->sum(DB::raw('prix * quantite'));

        // Coût des marchandises vendues sur la période
        $coutVentes = DB::table('vente_items as vi')
            ->join('ventes as v', 'v.id', '=', 'vi.vente_id')
            ->join(
                DB::raw('(SELECT produit_id, AVG(prix) as prix_moyen FROM achats GROUP BY produit_id) as a'),
                'a.produit_id', '=', 'vi.produit_id'
            )
            ->whereBetween('v.created_at', [$dateDebut, $dateFin])
            ->sum(DB::raw('vi.quantite * a.prix_moyen'));

        $benefice = $totalVentes - $coutVentes;

        // Top 5 produits vendus sur la période
        $topProduits = DB::table('vente_items')
            ->join('produits', 'vente_items.produit_id', '=', 'produits.id')
            ->join('ventes', 'vente_items.vente_id', '=', 'ventes.id')
            ->whereBetween('ventes.created_at', [$dateDebut, $dateFin])
            ->select('produits.nom', DB::raw('SUM(vente_items.quantite) as total_qte'), DB::raw('SUM(vente_items.total) as total_ca'))
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_ca')
            ->limit(5)
            ->get();

        // Ventes par jour pour le graphique
        $ventesParJour = Vente::whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('rapports.index', compact(
            'dateDebut', 'dateFin',
            'totalVentes', 'totalCredit', 'nbVentes',
            'totalAchats', 'benefice',
            'topProduits', 'ventesParJour'
        ));
    }

    // ── Exports Excel ─────────────────────────────────────────────────────────

    public function exportVentesExcel(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->startOfMonth();
        $dateFin   = $request->date_fin   ?? now()->endOfMonth();
        $filename  = 'ventes-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new VentesExport($dateDebut, $dateFin), $filename);
    }

    public function exportStocksExcel()
    {
        $filename = 'stocks-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new StocksExport(), $filename);
    }

    public function exportRapportCompletExcel(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->startOfMonth();
        $dateFin   = $request->date_fin   ?? now()->endOfMonth();
        $filename  = 'rapport-complet-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new RapportExport($dateDebut, $dateFin), $filename);
    }

    // ── Export PDF ────────────────────────────────────────────────────────────

    public function exportRapportPdf(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->startOfMonth()->format('Y-m-d');
        $dateFin   = $request->date_fin   ?? now()->endOfMonth()->format('Y-m-d');

        $ventes      = Vente::with(['items.produit', 'client'])
                            ->whereBetween('created_at', [$dateDebut, $dateFin])
                            ->latest()->get();
        $totalVentes = $ventes->sum('total');
        $totalAchats = Achat::whereBetween('date_achat', [$dateDebut, $dateFin])
                            ->sum(DB::raw('prix * quantite'));
        $coutVentes  = DB::table('vente_items as vi')
            ->join('ventes as v', 'v.id', '=', 'vi.vente_id')
            ->join(
                DB::raw('(SELECT produit_id, AVG(prix) as prix_moyen FROM achats GROUP BY produit_id) as a'),
                'a.produit_id', '=', 'vi.produit_id'
            )
            ->whereBetween('v.created_at', [$dateDebut, $dateFin])
            ->sum(DB::raw('vi.quantite * a.prix_moyen'));
        $benefice    = $totalVentes - $coutVentes;
        $produits    = Produit::orderBy('nom')->get();

        $pdf = Pdf::loadView('rapports.pdf', compact(
            'ventes', 'totalVentes', 'totalAchats', 'benefice',
            'produits', 'dateDebut', 'dateFin'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream('rapport-' . now()->format('Y-m-d') . '.pdf');
    }
}

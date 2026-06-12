<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Achat;
use App\Models\BonAchat;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\Categorie;

class AchatController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->input('search');
        $dateDebut   = $request->input('date_debut');
        $dateFin     = $request->input('date_fin');
        $fournId     = $request->input('fournisseur_id');
        $categorieId = $request->input('categorie_id');

        $filtreCategorie = fn($q) => $q->whereHas('achats.produit',
            fn($q2) => $q2->where('categorie_id', $categorieId));

        // Bons d'achat filtrés — avec catégorie chargée pour les badges
        $bons = BonAchat::with(['fournisseur', 'achats.produit.categorie'])
            ->when($fournId,      fn($q) => $q->where('fournisseur_id', $fournId))
            ->when($dateDebut,    fn($q) => $q->whereDate('date_achat', '>=', $dateDebut))
            ->when($dateFin,      fn($q) => $q->whereDate('date_achat', '<=', $dateFin))
            ->when($search,       fn($q) => $q->whereHas('achats.produit',
                fn($q2) => $q2->where('nom', 'like', "%{$search}%")))
            ->when($categorieId,  $filtreCategorie)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // KPIs globaux
        $totalGlobal    = Achat::sum(DB::raw('prix * quantite'));
        $achatsMois     = Achat::whereMonth('date_achat', now()->month)
                               ->whereYear('date_achat', now()->year)
                               ->sum(DB::raw('prix * quantite'));
        $nbFournisseurs = Fournisseur::count();

        // Total filtré
        $totalFiltré = BonAchat::when($fournId,     fn($q) => $q->where('fournisseur_id', $fournId))
            ->when($dateDebut,    fn($q) => $q->whereDate('date_achat', '>=', $dateDebut))
            ->when($dateFin,      fn($q) => $q->whereDate('date_achat', '<=', $dateFin))
            ->when($search,       fn($q) => $q->whereHas('achats.produit',
                fn($q2) => $q2->where('nom', 'like', "%{$search}%")))
            ->when($categorieId,  $filtreCategorie)
            ->sum('total');

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $categories   = Categorie::withCount(['produits' => fn($q) =>
            $q->whereHas('achats')])->orderBy('nom')->get();

        return view('achats.index', compact(
            'bons', 'search', 'dateDebut', 'dateFin', 'fournId', 'categorieId',
            'totalFiltré', 'totalGlobal', 'achatsMois', 'nbFournisseurs',
            'fournisseurs', 'categories'
        ));
    }

    public function create()
    {
        $produits     = Produit::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        return view('achats.create', compact('produits', 'fournisseurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id'    => 'required|exists:fournisseurs,id',
            'date_achat'        => 'required|date',
            'reference'         => 'nullable|string|max:100',
            'lignes'            => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite'   => 'required|integer|min:1',
            'lignes.*.prix'       => 'required|numeric|min:0',
        ]);

        // Pré-charger les produits pour vérification
        $ids      = collect($request->lignes)->pluck('produit_id')->unique();
        $produits = Produit::whereIn('id', $ids)->get()->keyBy('id');

        $totalGlobal = 0;
        foreach ($request->lignes as $ligne) {
            $totalGlobal += $ligne['quantite'] * $ligne['prix'];
        }

        DB::transaction(function () use ($request, $produits) {
            $bon = BonAchat::create([
                'fournisseur_id' => $request->fournisseur_id,
                'reference'      => $request->reference ?: null,
                'date_achat'     => $request->date_achat,
                'total'          => 0, // recalculé après insertion des lignes
            ]);

            foreach ($request->lignes as $ligne) {
                $produit = $produits[$ligne['produit_id']];

                Achat::create([
                    'bon_achat_id'   => $bon->id,
                    'fournisseur_id' => $request->fournisseur_id,
                    'produit_id'     => $ligne['produit_id'],
                    'quantite'       => $ligne['quantite'],
                    'prix'           => $ligne['prix'],
                    'date_achat'     => $request->date_achat,
                ]);

                $produit->increment('quantite', $ligne['quantite']);
            }

            // Total calculé depuis les lignes réelles — jamais de décalage
            $bon->recalculerTotal();
        });

        $nbLignes = count($request->lignes);
        return redirect()->route('achats.index')
                         ->with('success', "Bon d'achat enregistré — {$nbLignes} produit(s) réceptionné(s), stocks mis à jour.");
    }
}

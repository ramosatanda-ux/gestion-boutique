<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\VenteItem;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $dateDebut  = $request->input('date_debut');
        $dateFin    = $request->input('date_fin');
        $type       = $request->input('type'); // 'comptant', 'credit', ou vide

        $ventes = Vente::with('items.produit')
            ->when($search, fn($q) => $q->where('nom_client', 'like', "%{$search}%")
                                        ->orWhere('numero', 'like', "%{$search}%"))
            ->when($dateDebut, fn($q) => $q->whereDate('created_at', '>=', $dateDebut))
            ->when($dateFin,   fn($q) => $q->whereDate('created_at', '<=', $dateFin))
            ->when($type === 'comptant', fn($q) => $q->where('est_credit', false))
            ->when($type === 'credit',   fn($q) => $q->where('est_credit', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalFiltré = Vente::query()
            ->when($search, fn($q) => $q->where('nom_client', 'like', "%{$search}%")
                                        ->orWhere('numero', 'like', "%{$search}%"))
            ->when($dateDebut, fn($q) => $q->whereDate('created_at', '>=', $dateDebut))
            ->when($dateFin,   fn($q) => $q->whereDate('created_at', '<=', $dateFin))
            ->when($type === 'comptant', fn($q) => $q->where('est_credit', false))
            ->when($type === 'credit',   fn($q) => $q->where('est_credit', true))
            ->sum('total');

        return view('ventes.index', compact('ventes', 'search', 'dateDebut', 'dateFin', 'type', 'totalFiltré'));
    }

    public function create()
    {
        $produits = Produit::where('quantite', '>', 0)->orderBy('nom')->get();
        $clients  = Client::orderBy('nom')->get();
        return view('ventes.create', compact('produits', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produits'    => 'required|array|min:1',
            'produits.*'  => 'required|exists:produits,id',
            'quantites'   => 'required|array',
            'quantites.*' => 'required|integer|min:1',
            'reduction'   => 'nullable|integer|min:0',
        ]);

        $client = $request->client_id ? Client::find($request->client_id) : null;
        $limite = 10_000_000;

        // Vérifications crédit
        if ($request->est_credit) {
            if (!$client || !$client->est_particulier) {
                return back()->with('error', 'Seuls les clients particuliers peuvent acheter à crédit.');
            }
            if (!$client->a_credit) {
                return back()->with('error', "Ce client n'est pas autorisé au crédit.");
            }
            if ($client->solde >= $limite) {
                return back()->with('error', 'Client bloqué — dette trop élevée.');
            }
        }

        // Vérification stock ET calcul du total AVANT de toucher la base de données
        // CORRIGÉ : dans l'original, la vente était créée avec total=0 avant la vérification
        // du stock → en cas d'erreur, une vente vide restait en base
        $lignes      = [];
        $totalGlobal = 0;

        foreach ($request->produits as $index => $produit_id) {
            $quantite = (int) ($request->quantites[$index] ?? 0);
            if ($quantite <= 0) continue;

            $produit = Produit::findOrFail($produit_id);

            if ($quantite > $produit->quantite) {
                return back()->with('error', "Stock insuffisant pour « {$produit->nom} » (disponible : {$produit->quantite}).");
            }

            $lignes[] = [
                'produit'  => $produit,
                'quantite' => $quantite,
                'prix'     => $produit->prix,
                'total'    => $produit->prix * $quantite,
            ];

            $totalGlobal += $produit->prix * $quantite;
        }

        if (empty($lignes)) {
            return back()->with('error', 'Aucun produit valide sélectionné.');
        }

        $reduction = min((int) $request->reduction, $totalGlobal);
        $totalNet  = $totalGlobal - $reduction;

        // Vérification limite crédit avec le total après réduction
        if ($request->est_credit && $client && ($client->solde + $totalNet) > $limite) {
            return back()->with('error', 'Dette maximale dépassée après cette vente.');
        }

        $vente = DB::transaction(function () use ($lignes, $totalGlobal, $totalNet, $reduction, $client, $request) {
            $vente = Vente::create([
                'numero'     => 'V-' . strtoupper(uniqid()),
                'nom_client' => $request->nom_client,
                'telephone'  => $request->telephone,
                'adresse'    => $request->adresse,
                'client_id'  => $client?->id,
                'user_id'    => auth()->id(),
                'est_credit' => (bool) $request->est_credit,
                'reduction'  => $reduction,
                'total'      => $totalNet,
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

            if ($request->est_credit && $client) {
                $client->increment('solde', $totalNet);
            }

            return $vente;
        });

        return redirect()->route('ventes.index')
                         ->with('success', "Vente #{$vente->numero} enregistrée avec succès.");
    }

    public function facture($id)
    {
        // CORRIGÉ : Vente::with(...) était appelé 2 fois de suite
        $vente = Vente::with('items.produit', 'client')->findOrFail($id);

        return Pdf::loadView('ventes.facture', compact('vente'))
                  ->stream("facture-{$vente->numero}.pdf");
    }
}


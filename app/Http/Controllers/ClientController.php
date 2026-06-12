<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaiementClient;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\PayerClientRequest;
use Barryvdh\DomPDF\Facade\Pdf;


class ClientController extends Controller
{  
  public function show($id)
{
    $client = Client::with([
        'paiements' => fn($q) => $q->orderByDesc('created_at'),
        'ventes'    => fn($q) => $q->orderByDesc('created_at'),
    ])->findOrFail($id);

    $totalPaye   = $client->paiements->sum('montant');
    $totalAchat  = $client->ventes->sum('total');

    return view('clients.show', compact('client', 'totalPaye', 'totalAchat'));
} 
  public function index(Request $request)
{
    $search = $request->input('search');

    $clients = Client::query()
        ->when($search, fn($q) => $q->where('nom', 'like', "%{$search}%")
                                    ->orWhere('telephone', 'like', "%{$search}%"))
        ->orderBy('nom')
        ->paginate(20)
        ->withQueryString();

    return view('clients.index', compact('clients', 'search'));
}

public function create()
{
    return view('clients.create');
}

public function store(StoreClientRequest $request)
{
    Client::create([
        ...$request->validated(),
        'solde' => 0,
    ]);

    return redirect('/clients')->with('success', 'Client créé');
}
public function edit($id)
{
    $client = Client::findOrFail($id);
    return view('clients.edit', compact('client'));
}

public function update(Request $request, $id)
{
    $client = Client::findOrFail($id);

    $request->validate([
        'nom'             => 'required|string|max:255',
        'telephone'       => 'nullable|string|max:20',
        'adresse'         => 'nullable|string|max:500',
        'est_particulier' => 'boolean',
        'a_credit'        => 'boolean',
    ]);

    $client->update([
        'nom'             => $request->nom,
        'telephone'       => $request->telephone,
        'adresse'         => $request->adresse,
        'est_particulier' => $request->boolean('est_particulier'),
        'a_credit'        => $request->boolean('a_credit'),
    ]);

    return redirect()->route('clients.index')
                     ->with('success', "Client « {$client->nom} » modifié.");
}

public function destroy($id)
{
    $client = Client::findOrFail($id);
    $nom    = $client->nom;
    $client->delete();

    return redirect()->route('clients.index')
                     ->with('success', "Client « {$nom} » supprimé.");
}

public function debiteurs()
{
    $clients    = Client::where('solde', '>', 0)->orderByDesc('solde')->get();
    $totalDettes = $clients->sum('solde');

    return view('clients.debiteurs', compact('clients', 'totalDettes'));
}


public function payer(PayerClientRequest $request, $id)
{
    $client = Client::findOrFail($id);
    $montant = (float) $request->validated()['montant'];

    DB::transaction(function () use ($client, $montant) {
        PaiementClient::create([
            'client_id'      => $client->id,
            'montant'        => $montant,
            'date_paiement'  => now(),
        ]);

        $client->decrement('solde', $montant);
    });

    return redirect('/clients/debiteurs')->with('success', 'Paiement effectué');
}
public function payerForm($id)
{
    $client = Client::findOrFail($id);
    return view('clients.payer', compact('client'));
}
public function historiquePdf($id)
{
    $client = Client::with('paiements')->findOrFail($id);
    $totalPaye = $client->paiements->sum('montant');

    $pdf = Pdf::loadView('clients.historique_pdf', compact('client', 'totalPaye'));

    return $pdf->stream('historique-client.pdf');
}
}

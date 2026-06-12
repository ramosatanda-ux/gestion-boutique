<h2>Historique des paiements</h2>

<p><strong>Client :</strong> {{ $client->nom }}</p>
<p><strong>Total payé :</strong> {{ $totalPaye }} FCFA</p>
<p><strong>Dette restante :</strong> {{ $client->solde }} FCFA</p>

<hr>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Date</th>
            <th>Montant</th>
        </tr>
    </thead>
    <tbody>
        @foreach($client->paiements as $paiement)
        <tr>
            <td>{{ $paiement->created_at }}</td>
            <td>{{ $paiement->montant }} FCFA</td>
        </tr>
        @endforeach
    </tbody>
</table>
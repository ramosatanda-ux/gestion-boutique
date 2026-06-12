<table>
    <thead>
        <tr>
            <th colspan="7">
                RAPPORT DES VENTES — {{ now()->format('d/m/Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th colspan="7">
                Total : {{ number_format($totalGeneral, 0, ',', ' ') }} FCFA |
                Comptant : {{ number_format($totalComptant, 0, ',', ' ') }} FCFA |
                Crédit : {{ number_format($totalCredit, 0, ',', ' ') }} FCFA
            </th>
        </tr>
        <tr>
            <th>N° Vente</th>
            <th>Date</th>
            <th>Client</th>
            <th>Téléphone</th>
            <th>Type</th>
            <th>Nb produits</th>
            <th>Total (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventes as $vente)
        <tr>
            <td>{{ $vente->numero ?? 'V-'.$vente->id }}</td>
            <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $vente->nom_client }}</td>
            <td>{{ $vente->telephone ?? '—' }}</td>
            <td>{{ $vente->est_credit ? 'Crédit' : 'Comptant' }}</td>
            <td>{{ $vente->items->count() }}</td>
            <td>{{ number_format($vente->total, 0, ',', ' ') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6"><strong>TOTAL GÉNÉRAL</strong></td>
            <td><strong>{{ number_format($totalGeneral, 0, ',', ' ') }}</strong></td>
        </tr>
    </tbody>
</table>

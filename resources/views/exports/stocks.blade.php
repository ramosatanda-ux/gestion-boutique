<table>
    <thead>
        <tr>
            <th colspan="6">
                ÉTAT DES STOCKS — {{ now()->format('d/m/Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th colspan="6">
                Total produits : {{ $totalProduits }} |
                Ruptures : {{ $ruptures }} |
                Stock bas : {{ $stockBas }} |
                Valeur stock : {{ number_format($valeurStock, 0, ',', ' ') }} FCFA
            </th>
        </tr>
        <tr>
            <th>Produit</th>
            <th>Description</th>
            <th>Prix vente (FCFA)</th>
            <th>Quantité</th>
            <th>Stock minimum</th>
            <th>État</th>
        </tr>
    </thead>
    <tbody>
        @foreach($produits as $produit)
        <tr>
            <td>{{ $produit->nom }}</td>
            <td>{{ $produit->description ?? '—' }}</td>
            <td>{{ number_format($produit->prix, 0, ',', ' ') }}</td>
            <td>{{ $produit->quantite }}</td>
            <td>{{ $produit->stock_minimum ?? 5 }}</td>
            <td>
                @if($produit->quantite == 0)
                    RUPTURE
                @elseif($produit->quantite <= ($produit->stock_minimum ?? 5))
                    STOCK BAS
                @else
                    OK
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

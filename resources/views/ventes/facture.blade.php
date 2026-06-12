<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $vente->numero ?? '#'.$vente->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #222; }
        h2 { text-align: center; text-transform: uppercase; letter-spacing: 2px; }
        .credit-badge {
            text-align: center;
            margin: 12px 0;
        }
        .credit-badge span {
            display: inline-block;
            border: 3px solid red;
            color: red;
            font-size: 18px;
            font-weight: bold;
            padding: 8px 20px;
        }
        .info-bloc { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #aaa; padding: 6px 10px; }
        thead { background: #f0f0f0; }
        .total-row td { font-weight: bold; background: #f9f9f9; }
        .footer { margin-top: 24px; font-size: 12px; color: #555; text-align: center; }
    </style>
</head>
<body>

<h2>Facture</h2>

{{-- CORRIGÉ : "VENTE À CRÉDIT" n'apparaît plus qu'une seule fois --}}
@if($vente->est_credit)
    <div class="credit-badge">
        <span>⚠ VENTE À CRÉDIT</span>
    </div>
@endif

<p><strong>N° :</strong> {{ $vente->numero ?? 'V-'.$vente->id }}</p>
<p><strong>Date :</strong> {{ $vente->created_at->format('d/m/Y') }}</p>

<hr>

{{-- CORRIGÉ : les infos client n'apparaissent plus deux fois --}}
<div class="info-bloc">
    <p><strong>Client :</strong>
        @if($vente->client)
            {{ $vente->client->nom }}
        @else
            {{ $vente->nom_client }}
        @endif
    </p>
    <p><strong>Téléphone :</strong> {{ $vente->telephone ?? ($vente->client->telephone ?? '—') }}</p>
    <p><strong>Adresse :</strong> {{ $vente->adresse ?? ($vente->client->adresse ?? '—') }}</p>

    @if($vente->est_credit && $vente->client)
        <p><strong>Dette totale après cette vente :</strong>
            <span style="color:red;">{{ number_format($vente->client->solde, 0, ',', ' ') }} FCFA</span>
        </p>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php $totalGeneral = 0; @endphp

        @forelse($vente->items as $item)
            @php
                $ligneTotal = $item->quantite * $item->prix;
                $totalGeneral += $ligneTotal;
            @endphp
            <tr>
                <td>{{ $item->produit->nom ?? 'N/A' }}</td>
                <td>{{ $item->quantite }}</td>
                <td>{{ number_format($item->prix, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($ligneTotal, 0, ',', ' ') }} FCFA</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;">Aucun produit</td>
            </tr>
        @endforelse

        <tr>
            <td colspan="3" style="text-align:right; color:#555;">Sous-total</td>
            <td>{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($vente->reduction > 0)
        <tr>
            <td colspan="3" style="text-align:right; color:red;">Réduction</td>
            <td style="color:red;">− {{ number_format($vente->reduction, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        <tr class="total-row">
            <td colspan="3" style="text-align:right;">TOTAL NET À PAYER</td>
            <td>{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Merci pour votre achat — Document généré le {{ now()->format('d/m/Y à H:i') }}
</div>

</body>
</html> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport {{ $dateDebut }} au {{ $dateFin }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 4px; }
        .periode { text-align: center; color: #555; margin-bottom: 20px; font-size: 11px; }
        .kpis { display: flex; gap: 12px; margin-bottom: 20px; }
        .kpi { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 10px; text-align: center; }
        .kpi .val { font-size: 16px; font-weight: bold; }
        .kpi .lbl { font-size: 10px; color: #666; }
        .success { color: #16a34a; }
        .danger  { color: #dc2626; }
        .primary { color: #2563eb; }
        h2 { font-size: 14px; border-bottom: 2px solid #0f172a; padding-bottom: 4px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        th { background: #0f172a; color: white; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9fafb; }
        .total-row td { font-weight: bold; background: #f0f9ff; }
        .badge-credit   { background: #fef2f2; color: #991b1b; padding: 1px 6px; border-radius: 4px; }
        .badge-comptant { background: #f0fdf4; color: #166534; padding: 1px 6px; border-radius: 4px; }
        .footer { margin-top: 30px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>

<h1>Rapport de gestion</h1>
<div class="periode">
    Période du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
    au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
</div>

{{-- KPIs --}}
<div class="kpis">
    <div class="kpi">
        <div class="val success">{{ number_format($totalVentes, 0, ',', ' ') }} FCFA</div>
        <div class="lbl">Total ventes</div>
    </div>
    <div class="kpi">
        <div class="val primary">{{ number_format($totalAchats, 0, ',', ' ') }} FCFA</div>
        <div class="lbl">Total achats</div>
    </div>
    <div class="kpi">
        <div class="val {{ $benefice >= 0 ? 'success' : 'danger' }}">
            {{ number_format($benefice, 0, ',', ' ') }} FCFA
        </div>
        <div class="lbl">Bénéfice</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $ventes->count() }}</div>
        <div class="lbl">Nb ventes</div>
    </div>
</div>

{{-- Tableau des ventes --}}
<h2>Détail des ventes</h2>
<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>Date</th>
            <th>Client</th>
            <th>Type</th>
            <th>Total (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventes as $vente)
        <tr>
            <td>{{ $vente->numero ?? 'V-'.$vente->id }}</td>
            <td>{{ $vente->created_at->format('d/m/Y') }}</td>
            <td>{{ $vente->nom_client }}</td>
            <td>
                @if($vente->est_credit)
                    <span class="badge-credit">Crédit</span>
                @else
                    <span class="badge-comptant">Comptant</span>
                @endif
            </td>
            <td>{{ number_format($vente->total, 0, ',', ' ') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="4">TOTAL</td>
            <td>{{ number_format($totalVentes, 0, ',', ' ') }}</td>
        </tr>
    </tbody>
</table>

{{-- Tableau des stocks --}}
<h2>État des stocks</h2>
<table>
    <thead>
        <tr>
            <th>Produit</th>
            <th>Prix (FCFA)</th>
            <th>Quantité</th>
            <th>Valeur stock</th>
            <th>État</th>
        </tr>
    </thead>
    <tbody>
        @foreach($produits as $produit)
        <tr>
            <td>{{ $produit->nom }}</td>
            <td>{{ number_format($produit->prix, 0, ',', ' ') }}</td>
            <td>{{ $produit->quantite }}</td>
            <td>{{ number_format($produit->prix * $produit->quantite, 0, ',', ' ') }}</td>
            <td>
                @if($produit->quantite == 0)
                    <span style="color:#dc2626;font-weight:bold;">RUPTURE</span>
                @elseif($produit->quantite <= ($produit->stock_minimum ?? 5))
                    <span style="color:#d97706;font-weight:bold;">STOCK BAS</span>
                @else
                    <span style="color:#16a34a;">OK</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Document généré le {{ now()->format('d/m/Y à H:i') }} — Confidentiel
</div>

</body>
</html>

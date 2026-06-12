<?php

namespace App\Exports;

use App\Models\Produit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StocksExport implements FromView, ShouldAutoSize, WithTitle, WithStyles
{
    public function view(): View
    {
        $produits      = Produit::orderBy('nom')->get();
        $totalProduits = $produits->count();
        $ruptures      = $produits->where('quantite', 0)->count();
        $stockBas      = $produits->filter(fn($p) => $p->quantite > 0 && $p->quantite <= ($p->stock_minimum ?? 5))->count();
        $valeurStock   = $produits->sum(fn($p) => $p->prix * $p->quantite);

        return view('exports.stocks', compact(
            'produits',
            'totalProduits',
            'ruptures',
            'stockBas',
            'valeurStock'
        ));
    }

    public function title(): string
    {
        return 'Stocks';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F172A']],
            ],
        ];
    }
}

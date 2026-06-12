<?php

namespace App\Exports;

use App\Models\Vente;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentesExport implements FromView, ShouldAutoSize, WithTitle, WithStyles
{
    protected $dateDebut;
    protected $dateFin;

    public function __construct($dateDebut = null, $dateFin = null)
    {
        $this->dateDebut = $dateDebut ?? now()->startOfMonth();
        $this->dateFin   = $dateFin   ?? now()->endOfMonth();
    }

    public function view(): View
    {
        $ventes = Vente::with(['items.produit', 'client'])
            ->whereBetween('created_at', [$this->dateDebut, $this->dateFin])
            ->latest()
            ->get();

        $totalGeneral  = $ventes->sum('total');
        $totalCredit   = $ventes->where('est_credit', true)->sum('total');
        $totalComptant = $ventes->where('est_credit', false)->sum('total');

        return view('exports.ventes', compact(
            'ventes',
            'totalGeneral',
            'totalCredit',
            'totalComptant'
        ));
    }

    public function title(): string
    {
        return 'Ventes';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Ligne 1 (titre) : gras, grande taille
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Ligne d'en-tête du tableau
            4 => [
                'font'    => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F172A']],
            ],
        ];
    }
}

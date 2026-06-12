<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RapportExport implements WithMultipleSheets
{
    protected $dateDebut;
    protected $dateFin;

    public function __construct($dateDebut = null, $dateFin = null)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin   = $dateFin;
    }

    // Un fichier Excel avec 2 onglets : Ventes + Stocks
    public function sheets(): array
    {
        return [
            new VentesExport($this->dateDebut, $this->dateFin),
            new StocksExport(),
        ];
    }
}

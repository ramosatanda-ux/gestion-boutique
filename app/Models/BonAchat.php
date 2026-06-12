<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonAchat extends Model
{
    protected $fillable = ['fournisseur_id', 'reference', 'date_achat', 'total'];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function achats()
    {
        return $this->hasMany(Achat::class);
    }

    // Recalcule et sauvegarde le total depuis les achats réels
    public function recalculerTotal(): void
    {
        $this->update([
            'total' => $this->achats()->sum(\Illuminate\Support\Facades\DB::raw('prix * quantite')),
        ]);
    }
}

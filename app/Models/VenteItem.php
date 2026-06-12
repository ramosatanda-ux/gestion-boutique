<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenteItem extends Model
{
    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix',
        'total',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class); // ← AJOUTÉ : manquait dans l'original
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [   // ← AJOUTÉ : manquait totalement
        'numero',
        'nom_client',
        'telephone',
        'adresse',
        'vente_id',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class); // ← AJOUTÉ
    }

    public function items()
    {
        return $this->hasMany(VenteItem::class);
    }
}

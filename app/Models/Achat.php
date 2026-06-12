<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achat extends Model
{
    protected $fillable = [
        'bon_achat_id',
        'fournisseur_id',
        'produit_id',
        'quantite',
        'prix',
        'date_achat'
    ];

    // Accessor pour calculer le total automatiquement
    public function getTotalAttribute(): int
    {
        return $this->quantite * $this->prix;
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function bonAchat()
    {
        return $this->belongsTo(BonAchat::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'nom',
        'categorie_id',
        'code_barre',
        'description',
        'image',
        'prix',
        'quantite',
        'stock_minimum',
    ];

    protected $casts = [
        'prix' => 'decimal:0', // Pas de centimes (FCFA), changer en 'decimal:2' si besoin
    ];

    // Vérifie si le stock est sous le seuil minimum
    public function stockBas(): bool
    {
        return $this->quantite <= ($this->stock_minimum ?? 5);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function venteItems()
    {
        return $this->hasMany(VenteItem::class);
    }

    public function achats()
    {
        return $this->hasMany(Achat::class);
    }
}
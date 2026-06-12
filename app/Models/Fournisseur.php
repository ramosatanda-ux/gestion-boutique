<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
 protected $fillable = [
    'nom',
    'prenom',
    'telephone',
    'adresse'
];

// Relation avec achats
public function achats()
{
    return $this->hasMany(Achat::class);
}
}

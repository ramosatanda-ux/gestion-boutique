<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaiementClient;

class Client extends Model
{  
 
    protected $fillable = [
        'nom',
        'telephone',
        'adresse',
        'a_credit',
        'solde',
        'est_particulier'
    ];

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function paiements()
    {
        return $this->hasMany(PaiementClient::class);
    }
}

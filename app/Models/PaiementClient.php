<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaiementClient extends Model
{
    protected $fillable = ['client_id', 'montant', 'date_paiement'];

    protected $casts = [
        'date_paiement' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
}

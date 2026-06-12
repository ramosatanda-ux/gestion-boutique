<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'total',
        'reduction',
        'numero',
        'nom_client',
        'telephone',
        'adresse',
        'est_credit',
    ];

    protected $casts = [
        'est_credit' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(VenteItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
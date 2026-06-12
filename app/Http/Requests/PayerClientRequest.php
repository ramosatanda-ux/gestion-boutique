<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Client;

class PayerClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $client = Client::find($this->route('id'));
        $soldeMax = $client ? $client->solde : 0;

        return [
            'montant' => "required|numeric|min:0.01|max:{$soldeMax}",
        ];
    }

    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.min'      => 'Le montant doit être supérieur à 0.',
            'montant.max'      => 'Le montant ne peut pas dépasser la dette du client.',
        ];
    }
}

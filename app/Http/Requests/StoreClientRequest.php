<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'             => 'required|string|max:255',
            'telephone'       => 'nullable|string|max:20',
            'adresse'         => 'nullable|string|max:500',
            'est_particulier' => 'boolean',
            'a_credit'        => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du client est obligatoire.',
            'nom.max'      => 'Le nom ne peut pas dépasser 255 caractères.',
        ];
    }
}

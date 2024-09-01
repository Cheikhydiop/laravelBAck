<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize()
    {
        // Autoriser cette requête pour tous les utilisateurs
        return true;
    }

    public function rules()
    {
        return [
            'quantite' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.numeric' => 'La quantité doit être un nombre.',
            'quantite.min' => 'La quantité doit être au moins de 0.',
        ];
    }
}

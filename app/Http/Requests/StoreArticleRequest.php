<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'articles' => 'required|array|min:1',
            'articles.*.libelle' => 'required|string|unique:articles,libelle',
            'articles.*.quantite' => 'required|integer|min:1', // La quantité doit être un entier positif
            'articles.*.prix' => 'required|numeric|min:0.01',  // Le prix doit être un nombre positif (au moins 0.01)
            'articles.*.reference' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'articles.required' => 'Vous devez fournir au moins un article.',
            'articles.array' => 'La liste des articles doit être un tableau.',
            'articles.min' => 'La liste des articles doit contenir au moins un élément.',
            'articles.*.libelle.required' => 'Le libellé de l\'article est requis.',
            'articles.*.libelle.unique' => 'Le libellé de l\'article doit être unique.',
            'articles.*.quantite.required' => 'La quantité de l\'article est requise.',
            'articles.*.quantite.integer' => 'La quantité de l\'article doit être un entier.',
            'articles.*.quantite.min' => 'La quantité de l\'article doit être un entier positif.',
            'articles.*.prix.required' => 'Le prix de l\'article est requis.',
            'articles.*.prix.numeric' => 'Le prix de l\'article doit être un nombre.',
            'articles.*.prix.min' => 'Le prix de l\'article doit être un nombre positif (au moins 0.01).',
            'articles.*.reference.required' => 'La référence de l\'article est requise.',
            'articles.*.reference.max' => 'La référence de l\'article ne doit pas dépasser 255 caractères.',
        ];
    }
}

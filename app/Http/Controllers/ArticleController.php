<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\ArticleRessource;
use App\Traits\RestResponseTrait;
use App\Enums\StatusResponseEnum;
use Exception;

class ArticleController extends Controller
{
    use RestResponseTrait;

    public function verification(Request $request)
    {
        try {
            // Récupérer le paramètre 'disponible' de la requête
            $disponible = $request->query('disponible');
    
            // Créer la requête de base pour les articles
            $query = Article::query();
    
            // Appliquer le filtrage conditionnel en fonction du paramètre 'disponible'
            $query->when($disponible, function ($query, $disponible) {
                if ($disponible === 'oui') {
                    $query->where('quantite', '>', 0);
                } elseif ($disponible === 'non') {
                    $query->where('quantite', '=', 0);
                }
            });
    
            // Pagination
            $perPage = $request->query('per_page', 10); 
            $articles = $query->paginate($perPage);
    
            // Retourner les résultats sous forme de réponse
            $resource = ArticleRessource::collection($articles);
            
            return $this->sendResponse($resource, StatusResponseEnum::SUCCESS, 'Liste des articles');
        } catch (Exception $e) {
            return $this->sendError('Erreur lors de la récupération des articles : ' . $e->getMessage(), [], 500);
        }
    }
    

    public function updateStock(Request $request)
{
    // Valider les données entrantes
    // dd($request);
  



    $validatedData = $request->validate([
        'articles' => 'required|array',
        'articles.*.id' => 'required|integer|exists:articles,id',
        'articles.*.libelle' => 'required|string|max:255',
        'articles.*.quantite' => 'required|integer',
    ]);

    foreach ($validatedData['articles'] as $articleData) {
        // Trouver l'article par ID
        $article = Article::find($articleData['id']);

        if ($article) {
            // Vérifier si le libellé correspond
            if ($article->libelle === $articleData['libelle']) {
                // Mettre à jour la quantité en ajoutant la nouvelle quantité
                $article->quantite += $articleData['quantite'];
                $article->save();
            }
        }
    }

    return response()->json(['message' => 'Stock mis à jour avec succès.']);
}

public function storeArticle(Request $request)
{
    $validatedData = $request->validate([
        'articles' => 'required|array',
        'articles.*.libelle' => 'required|string|unique:articles,libelle',
        'articles.*.quantite' => 'required|integer|min:0',
        'articles.*.prix' => 'required|numeric|min:0',
        'articles.*.reference' => 'required|string|max:255', // Ajouter cette ligne si `reference` est requis
    ]);

    $addedArticles = [];
    $errors = [];

    foreach ($validatedData['articles'] as $articleData) {
        try {
            $article = Article::create([
                'libelle' => $articleData['libelle'],
                'quantite' => $articleData['quantite'],
                'prix' => $articleData['prix'],
                'reference' => $articleData['reference'], // Assurez-vous que cela correspond à votre base de données
            ]);
            
            $addedArticles[] = new ArticleRessource($article);
        } catch (\Exception $e) {
            $errors[] = [
                'message' => 'Erreur lors de l\'ajout de l\'article.',
                'error' => $e->getMessage()
            ];
        }
    }

    if (!empty($errors)) {
        return response()->json([
            'message' => 'Certains articles n\'ont pas pu être ajoutés.',
            'errors' => $errors
        ], 400);
    }

    return response()->json([
        'message' => 'Articles ajoutés avec succès.',
        'added_articles' => $addedArticles
    ], 201);
}

}

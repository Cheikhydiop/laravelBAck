<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\ArticleRessource;
use App\Traits\RestResponseTrait;
use App\Enums\StatusResponseEnum;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;


use Exception;

class ArticleController extends Controller
{
    use RestResponseTrait;

    public function verification(Request $request)
    {
        try {
            $disponible = $request->query('disponible');
    
            $query = Article::query();
    
            $query->when($disponible, function ($query, $disponible) {
                if ($disponible === 'oui') {
                    $query->where('quantite', '>', 0);
                } elseif ($disponible === 'non') {
                    $query->where('quantite', '=', 0);
                }
            });
    
            $perPage = $request->query('per_page', 10); 
            $articles = $query->paginate($perPage);
    
            $resource = ArticleRessource::collection($articles);
            
            return $this->sendResponse($resource, StatusResponseEnum::SUCCESS, 'Liste des articles');
        } catch (Exception $e) {
            return $this->sendError('Erreur lors de la récupération des articles : ' . $e->getMessage(), [], 500);
        }
    }
    
    public function updateStock(Request $request)
    {
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
                if ($article->libelle === $articleData['libelle']) {
                    $article->quantite += $articleData['quantite'];
                    $article->save();
                }
            }
        }

        return response()->json(['message' => 'Stock mis à jour avec succès.']);
    }

    public function storeArticle(StoreArticleRequest $request)
    {
        $validatedData = $request->validated(); 

        $addedArticles = [];
        $errors = [];

        foreach ($validatedData['articles'] as $articleData) {
            try {
                $article = Article::create([
                    'libelle' => $articleData['libelle'],
                    'quantite' => $articleData['quantite'],
                    'prix' => $articleData['prix'],
                    'reference' => $articleData['reference'],
                ]);
                
                $addedArticles[] = new ArticleResource($article);
            } catch (\Exception $e) {
                $errors[] = [
                    'message' => 'Erreur lors de l\'ajout de l\'article.',
                    'error' => $e->getMessage(),
                ];
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Certains articles n\'ont pas pu être ajoutés.',
                'errors' => $errors,
            ], 400);
        }

        return response()->json([
            'message' => 'Articles ajoutés avec succès.',
            'added_articles' => $addedArticles,
        ], 201);
    }
    public function show($id)
    {
        try {
            $article = Article::find($id);

            if ($article) {
                return $this->sendResponse(new ArticleRessource($article), StatusResponseEnum::SUCCESS, 'Article trouvé');
            } else {
                return $this->sendError('Article non trouvé', [], 404);
            }
        } catch (Exception $e) {
            return $this->sendError('Erreur lors de la récupération de l\'article : ' . $e->getMessage(), [], 500);
        }
    }

    public function findByLibelle(Request $request)
    {
        try {
            $libelle = $request->input('libelle');
            $article = Article::where('libelle', $libelle)->first();
    
            if ($article) {
                return $this->sendResponse(new ArticleRessource($article), StatusResponseEnum::SUCCESS, 'Article trouvé');
            } else {
                return $this->sendError('Objet non trouvé', [], 411);
            }
        } catch (Exception $e) {
            return $this->sendError('Erreur lors de la recherche de l\'article : ' . $e->getMessage(), [], 500);
        }
    }
    

    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
    
            // Obtenir la nouvelle quantité depuis la requête
            $newQuantite = $request->input('quantite');
    
            // Vérifier si la nouvelle quantité est valide
            if ($newQuantite < 0) {
            
                return $this->sendError('La quantité ne peut pas être négative.', [], 400);
            }
    
            // Ajouter la nouvelle valeur à l'ancienne valeur de la quantité
            $article->quantite += $newQuantite;
    
            // Sauvegarder l'article avec la nouvelle quantité
            $article->save();
    
            return $this->sendResponse(new ArticleRessource($article), StatusResponseEnum::SUCCESS, 'Quantité mise à jour avec succès');
        } catch (Exception $e) {
            return $this->sendError('Erreur lors de la mise à jour de l\'article : ' . $e->getMessage(), [], 500);
        }
    }
     
    
    
    
}

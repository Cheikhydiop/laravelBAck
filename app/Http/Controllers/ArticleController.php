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

/**
 * @OA\Get(
 *     path="/api/v1/article",
 *     summary="Get articles with optional availability filter",
 *     tags={"Articles"},
 *     @OA\Parameter(
 *         name="disponible",
 *         in="query",
 *         description="Filter by availability",
 *         required=false,
 *         @OA\Schema(type="string", enum={"oui", "non"})
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of articles",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ArticleResource")),
 *             @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
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

    /**
     * @OA\Post(
     *     path="/api/v1/articles/updatestock",
     *     summary="Update stock quantities for multiple articles",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="libelle", type="string", example="Article name"),
     *                     @OA\Property(property="quantite", type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock mis à jour avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/articles",
     *     summary="Store new articles",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="libelle", type="string", example="Article name"),
     *                     @OA\Property(property="quantite", type="integer", example=10),
     *                     @OA\Property(property="prix", type="number", format="float", example=19.99),
     *                     @OA\Property(property="reference", type="string", example="REF123")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Articles created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Articles ajoutés avec succès."),
     *             @OA\Property(property="added_articles", type="array", @OA\Items(ref="#/components/schemas/ArticleResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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
                
                $addedArticles[] = new ArticleRessource($article);
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

    /**
     * @OA\Get(
     *     path="/api/v1/articles/{id}",
     *     summary="Get a single article by ID",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/ArticleResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article non trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/articles/libelle",
     *     summary="Find an article by its label",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="libelle",
     *         in="query",
     *         description="Article label",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/ArticleResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/articles/{id}",
     *     summary="Update the quantity of an article",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="quantite", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantity updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ArticleResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid quantity value",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La quantité ne peut pas être négative.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article non trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
    
         
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

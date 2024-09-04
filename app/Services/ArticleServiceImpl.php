<?php
namespace App\Services;

use App\Facades\ArticleRepositoryFacade;
use App\Http\Resources\ArticleRessource;
use Exception;

class ArticleServiceImpl
{
    protected $articleRepositoryFacade;

    public function __construct(ArticleRepositoryFacade $articleRepositoryFacade)
    {
        $this->articleRepositoryFacade = $articleRepositoryFacade;
    }

    public function getArticles($disponible, $perPage)
    {
        return $this->articleRepositoryFacade->getArticles($disponible, $perPage);
    }

    public function findArticleById($id)
    {
        $article = $this->articleRepositoryFacade->findArticleById($id);
        if ($article) {
            return new ArticleRessource($article);
        } else {
            throw new Exception('Article non trouvé');
        }
    }

    public function findArticleByLibelle($libelle)
    {
        $article = $this->articleRepositoryFacade->findArticleByLibelle($libelle);
        if ($article) {
            return new ArticleRessource($article);
        } else {
            throw new Exception('Objet non trouvé');
        }
    }

    public function updateStock(array $articlesData)
    {
        $this->articleRepositoryFacade->updateStock($articlesData);
        return ['message' => 'Stock mis à jour avec succès.'];
    }

    public function storeArticles(array $articlesData)
    {
        $addedArticles = $this->articleRepositoryFacade->storeArticles($articlesData);
        return [
            'message' => 'Articles ajoutés avec succès.',
            'added_articles' => ArticleRessource::collection($addedArticles),
        ];
    }
}

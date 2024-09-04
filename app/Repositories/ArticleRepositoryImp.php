<?php
namespace App\Repositories;

use App\Models\Article;

class ArticleRepositoryImp
{
    public function getArticles($disponible = null, $perPage = 10)
    {
        $query = Article::query();

        if ($disponible === 'oui') {
            $query->disponible();
        } elseif ($disponible === 'non') {
            $query->nonDisponible();
        }

        return $query->paginate($perPage);
    }

    public function findArticleById($id)
    {
        return Article::find($id);
    }

    public function findArticleByLibelle($libelle)
    {
        return Article::where('libelle', $libelle)->first();
    }

    public function updateStock(array $articlesData)
    {
        foreach ($articlesData as $articleData) {
            $article = Article::find($articleData['id']);
            if ($article && $article->libelle === $articleData['libelle']) {
                $article->quantite += $articleData['quantite'];
                $article->save();
            }
        }
    }

    public function storeArticles(array $articlesData)
    {
        $addedArticles = [];

        foreach ($articlesData as $articleData) {
            $article = Article::create($articleData);
            $addedArticles[] = $article;
        }

        return $addedArticles;
    }
}

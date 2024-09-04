<?php

namespace App\Facades;

use App\Repositories\ArticleRepositoryImp;

class ArticleRepositoryFacade
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryImp $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getArticles($disponible, $perPage)
    {
        return $this->articleRepository->getArticles($disponible, $perPage);
    }

    public function findArticleById($id)
    {
        return $this->articleRepository->findArticleById($id);
    }

    public function findArticleByLibelle($libelle)
    {
        return $this->articleRepository->findArticleByLibelle($libelle);
    }

    public function updateStock(array $articlesData)
    {
        return $this->articleRepository->updateStock($articlesData);
    }

    public function storeArticles(array $articlesData)
    {
        return $this->articleRepository->storeArticles($articlesData);
    }
}

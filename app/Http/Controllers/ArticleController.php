<?php

namespace App\Http\Controllers;

use App\Services\ArticleServiceImpl;
use Illuminate\Http\Request;
use Exception;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceImpl $articleService)
    {
        $this->articleService = $articleService;
    }

    public function verification(Request $request)
    {
        try {
            $disponible = $request->query('disponible');
            $perPage = $request->query('per_page', 10);
            return $this->articleService->getArticles($disponible, $perPage);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            return $this->articleService->findArticleById($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
        $articlesData = $request->all();
        return $this->articleService->storeArticles($articlesData);
    }

    public function updateStock(Request $request)
    {
        $articlesData = $request->input('articles');
        return $this->articleService->updateStock($articlesData);
    }
}

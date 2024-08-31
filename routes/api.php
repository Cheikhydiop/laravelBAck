<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;


use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/stock', [ArticleController::class, 'updateStock']);

});


// // Routes pour la gestion des articles
// oute::prefix('v1/articles')->group(function () {
//     Route::get('/', [ArticleController::class, 'index'])->name('articles.index'); // GET http://localhost:3000/api/v1/articles
//     Route::get('/{id}', [ArticleController::class, 'show']); // GET http://localhost:3000/api/v1/articles/id
    Route::get('/v1/article', [ArticleController::class, 'verification']);
    Route::post('/v1/storeArticle', [ArticleController::class, 'storeArticle']);
    Route::get('/v1/users', [UserController::class, 'getUsers']);


    





    



//     Route::post('/', [ArticleController::class, 'store']); // POST http://localhost:3000/api/v1/articles
//     Route::post('/libelle', [ArticleController::class, 'storeWithLibelle']); // POST http://localhost:3000/api/v1/articles/libelle
//     Route::put('/stock', [ArticleController::class, 'updateStock']); // PUT http://localhost:3000/api/v1/articles/stock
//     Route::patch('/{id}', [ArticleController::class, 'update']); // PATCH http://localhost:3000/api/v1/articles/id
//     Route::delete('/{id}', [ArticleController::class, 'destroy']); // DELETE http://localhost:3000/api/v1/articles/id
// });

// // Routes pour l'authentification
// Route::post('/login', [AuthController::class, 'login']); // POST http://localhost:3000/api/v1/login
// Route::post('/register', [AuthController::class, 'register']); // POST http://localhost:3000/api/v1/register


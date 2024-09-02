<?php

// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\ArticleController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\ClientController;
// use Illuminate\Support\Facades\Route;

// // Routes non protégées
// Route::prefix('v1')->group(function () {
//     Route::post('/register', [AuthController::class, 'register']);
// });
// Route::post('/v1/login', [AuthController::class, 'login'])->name('login');
// Route::get('/v1/login', [AuthController::class, 'login'])->name('login');


// // Routes protégées par l'authentification
// Route::middleware('auth:api')->prefix('v1')->group(function () {
//     Route::post('/stock', [ArticleController::class, 'updateStock']);
//     Route::get('/article', [ArticleController::class, 'verification']);
//     Route::post('/storeArticle', [ArticleController::class, 'storeArticle']);
//     Route::get('/users', [UserController::class, 'getUsers']);
//     Route::post('/users', [UserController::class, 'store']);
//     Route::get('/users', [UserController::class, 'index']);
//     Route::get('/articles/{id}', [ArticleController::class, 'show']);
//     Route::post('/articles/libelle', [ArticleController::class, 'findByLibelle']);
//     Route::patch('/articles/{id}', [ArticleController::class, 'update']);
//     Route::get('/clients', [ClientController::class, 'index']);
//     Route::post('/clients', [ClientController::class, 'store']);
//     Route::post('/clients/telephone', [ClientController::class, 'getByTelephone']);
// });


use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TestController
;

use Illuminate\Support\Facades\Route;

// Routes non protégées
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/login', [AuthController::class, 'login'])->name('login');
    // Route::get('/login', [AuthController::class, 'login'])->name('login');
    // Route pour le login POST


    Route::post('/logout', [AuthController::class, 'logout'])->name('login.get');


Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/login', [AuthController::class, 'login'])->name('login.get');

});

// Routes protégées par l'authentification
Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::post('/stock', [ArticleController::class, 'updateStock']);
    Route::get('/article', [ArticleController::class, 'verification']);
    Route::post('/storeArticle', [ArticleController::class, 'storeArticle']);
    
    // Routes protégées par la politique d'administrateur
    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::get('/users', [UserController::class, 'getUsers']);
        Route::post('/store', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
    });

    Route::get('/articles/{id}', [ArticleController::class, 'show']);
    Route::post('/articles/libelle', [ArticleController::class, 'findByLibelle']);
    Route::patch('/articles/{id}', [ArticleController::class, 'update']);
    Route::get('/articles/{id}', [ArticleController::class, 'update']);

    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('/storeClient', [ClientController::class, 'store']);
    Route::get('/clients/{telephone}', [ClientController::class, 'getByTelephone']);
    Route::get('/clients/{id}/user', [ClientController::class, 'clientWithUser']);
});
Route::get('/test', [TestController::class, 'testEndpoint']);

<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Api\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get(
    '/articles',
    [App\Http\Controllers\Api\ArticleController::class, 'index']
);

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);

    Route::post('/favorite/authors/{author}', [FavoriteController::class, 'toggleFavoriteAuthor']);
    Route::get('/favorite/authors', [FavoriteController::class, 'getFavoriteAuthors']);

    Route::post('/favorite/categories/{category}', [FavoriteController::class, 'toggleFavoriteCategory']);
    Route::get('/favorite/categories', [FavoriteController::class, 'getFavoriteCategories']);

    Route::get('/favorite/articles', [FavoriteController::class, 'getFavoriteArticles']);
});

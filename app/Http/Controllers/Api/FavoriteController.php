<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteController extends Controller
{
    protected $favoriteRepository;

    public function __construct(FavoriteRepositoryInterface $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function toggleFavoriteAuthor(Author $author)
    {
        $userId = Auth::id();
        $response = $this->favoriteRepository->toggleFavoriteAuthor($userId, $author->id);

        return response()->json($response);
    }

    public function getFavoriteAuthors()
    {
        $userId = Auth::id();
        $favoriteAuthors = $this->favoriteRepository->getFavoriteAuthors($userId);

        return response()->json($favoriteAuthors);
    }

    public function toggleFavoriteCategory(Category $category)
    {
        $userId = Auth::id();
        $response = $this->favoriteRepository->toggleFavoriteCategory($userId, $category->id);

        return response()->json($response);
    }

    public function getFavoriteCategories()
    {
        $userId = Auth::id();
        $favoriteCategories = $this->favoriteRepository->getFavoriteCategories($userId);

        return response()->json($favoriteCategories);
    }

    public function getFavoriteArticles()
    {
        $userId = Auth::id();
        $articles = $this->favoriteRepository->getFavoriteArticles($userId);

        return response()->json($articles);
    }
}
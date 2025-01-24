<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function toggleFavoriteAuthor($userId, $authorId)
    {
        $user = User::findOrFail($userId);
        $user->favoriteAuthors()->toggle($authorId);

        return [
            'message' => 'Favorite author toggled successfully.',
        ];
    }

    public function getFavoriteAuthors($userId)
    {
        return User::findOrFail($userId)->favoriteAuthors;
    }

    public function toggleFavoriteCategory($userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $user->favoriteCategories()->toggle($categoryId);

        return [
            'message' => 'Favorite category toggled successfully.',
        ];
    }

    public function getFavoriteCategories($userId)
    {
        return User::findOrFail($userId)->favoriteCategories;
    }

    public function getFavoriteArticles($userId)
    {
        $user = User::findOrFail($userId);

        $favoriteAuthorsArticles = $user->favoriteAuthors()
            ->with('articles')
            ->get()
            ->pluck('articles')
            ->flatten();

        $favoriteCategoriesArticles = $user->favoriteCategories()
            ->with('articles')
            ->get()
            ->pluck('articles')
            ->flatten();

        // Merge and deduplicate articles
        return $favoriteAuthorsArticles->merge($favoriteCategoriesArticles)
            ->unique('id')
            ->values();
    }
}
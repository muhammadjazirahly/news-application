<?php

namespace App\Repositories\Contracts;

interface FavoriteRepositoryInterface
{
    public function toggleFavoriteAuthor($userId, $authorId);
    public function getFavoriteAuthors($userId);
    public function toggleFavoriteCategory($userId, $categoryId);
    public function getFavoriteCategories($userId);
    public function getFavoriteArticles($userId);
}
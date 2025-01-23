<?php

namespace App\Repositories\Contracts;

use App\Models\Article;

interface ArticleRepositoryInterface
{
    public function createOrUpdate(array $articleData): Article;
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function createOrUpdate(array $articleData): Article;
    public function filter(array $request): LengthAwarePaginator; 
}

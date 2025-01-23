<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function createOrUpdate(array $articleData): Article
    {
        return Article::updateOrCreate(
            [
                'source' => $articleData['source'],
                'source_id' => $articleData['source_id']
            ],
            $articleData
        );
    }
}

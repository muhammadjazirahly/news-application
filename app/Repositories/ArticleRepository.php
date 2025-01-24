<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function filter(array $request): LengthAwarePaginator
    {
        $query = Article::query()->with(['authors', 'categories']);

        if (!empty($request['author'])) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request['author']}%");
            });
        }

        if (!empty($request['category'])) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request['category']}%");
            });
        }

        if (!empty($request['published_at'])) {
            $query->whereDate('published_at', $request['published_at']);
        }

        return $query->latest()->paginate($request['per_page'] ?? 10);
    }
}

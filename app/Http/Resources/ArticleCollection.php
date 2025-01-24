<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($article) {
                return [
                    'id'           => $article->id,
                    'title'        => $article->title,
                    'content'      => $article->content,
                    'published_at' => $article->published_at,
                    'authors'      => $article->authors->pluck('name'),
                    'categories'   => $article->categories->pluck('name'),
                    'source'       => $article->source,
                ];
            }),
        ];
    }
}
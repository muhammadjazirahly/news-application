<?php

namespace App\Services\News;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\News\DataTransferObjects\ArticleDTO;

class NewsSyncService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private AuthorRepositoryInterface $authorRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function syncArticle(ArticleDTO $articleDTO): void
    {
        $authorIds = [];
        foreach ($articleDTO->authors as $authorName) {
            $author = $this->authorRepository->firstOrCreate([
                'name' => $authorName,
                'source' => $articleDTO->source
            ]);
            $authorIds[] = $author->id;
        }

        $categoryIds = [];
        foreach ($articleDTO->categories as $categoryData) {
            $category = $this->categoryRepository->firstOrCreate([
                'name' => $categoryData['name'],
                'source' => $categoryData['source']
            ]);
            $categoryIds[] = $category->id;
        }

        $article = $this->articleRepository->createOrUpdate([
            'title' => $articleDTO->title,
            'content' => $articleDTO->content,
            'url' => $articleDTO->url,
            'published_at' => $articleDTO->publishedAt,
            'source' => $articleDTO->source,
            'source_id' => $articleDTO->sourceId
        ]);

        $article->authors()->sync($authorIds);
        $article->categories()->sync($categoryIds);
    }
}

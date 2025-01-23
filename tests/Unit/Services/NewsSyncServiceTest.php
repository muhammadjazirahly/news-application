<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\News\NewsSyncService;
use App\Services\News\DataTransferObjects\ArticleDTO;
use Carbon\Carbon;

class NewsSyncServiceTest extends TestCase
{
    public function test_syncs_article_with_authors_and_categories()
    {
        $dto = new ArticleDTO(
            source: 'guardian',
            sourceId: '123',
            title: 'Test Article',
            content: 'Test content',
            url: 'https://example.com',
            publishedAt: Carbon::now(),
            authors: ['Author 1', 'Author 2'],
            categories: [ // Wrap in array
                ['name' => 'Tech', 'source' => 'guardian']
            ]
        );

        $service = app(NewsSyncService::class);
        $service->syncArticle($dto);

        $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
        $this->assertDatabaseHas('authors', ['name' => 'Author 1']);
        $this->assertDatabaseHas('categories', ['name' => 'Tech']);
    }
}

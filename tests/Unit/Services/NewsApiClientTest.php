<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\News\NewsApiClient;
use Illuminate\Support\Facades\Http;

class NewsApiClientTest extends TestCase
{
    public function test_fetches_and_transforms_articles()
    {
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Test News',
                        'url' => 'https://news.com',
                        'publishedAt' => now()->toIso8601String(),
                        'content' => 'News content',
                        'source' => ['name' => 'BBC'],
                        'author' => 'John Doe'
                    ]
                ]
            ])
        ]);

        $client = new NewsApiClient();
        $articles = $client->fetchArticles(now()->subHour());

        $this->assertCount(1, $articles);
        $this->assertEquals('Test News', $articles[0]->title);
        $this->assertEquals('John Doe', $articles[0]->authors[0]);
    }
}

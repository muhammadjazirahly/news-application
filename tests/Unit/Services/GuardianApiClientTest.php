<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Carbon\Carbon;
use App\Services\News\GuardianApiClient;
use App\Exceptions\ApiRateLimitException;
use Illuminate\Support\Facades\Http;

class GuardianApiClientTest extends TestCase
{
    public function test_fetches_and_transforms_articles_with_various_authors()
    {
        Http::fake([
            'content.guardianapis.com/search*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'id' => 'test-123',
                            'webTitle' => 'Test Article 1',
                            'webUrl' => 'https://example.com/1',
                            'webPublicationDate' => now()->toIso8601String(),
                            'fields' => [
                                'body' => '<p>Test content</p>',
                                'byline' => 'By John Smith, Jane Doe and Alice Brown'
                            ],
                            'sectionName' => 'Technology',
                            'type' => 'article'
                        ],
                        [
                            'id' => 'test-456',
                            'webTitle' => 'Test Article 2',
                            'webUrl' => 'https://example.com/2',
                            'webPublicationDate' => now()->subDay()->toIso8601String(),
                            'fields' => [
                                'body' => '<p>Another test content</p>',
                                'byline' => 'By Carlos Hernández (Editor)'
                            ],
                            'sectionName' => 'World News',
                            'type' => 'article'
                        ]
                    ]
                ]
            ])
        ]);

        $client = new GuardianApiClient();
        $articles = $client->fetchArticles(now()->subWeek());

        // Test article count
        $this->assertCount(2, $articles);

        // Test first article
        $firstArticle = $articles[0];
        $this->assertEquals('Test Article 1', $firstArticle->title);
        $this->assertEquals('https://example.com/1', $firstArticle->url);
        $this->assertEquals('Test content', $firstArticle->content);
        $this->assertEquals(['John Smith', 'Jane Doe', 'Alice Brown'], $firstArticle->authors);
        $this->assertEquals('technology', $firstArticle->categories[0]['name']);
        $this->assertEquals('guardian', $firstArticle->categories[0]['source']);
        $this->assertInstanceOf(Carbon::class, $firstArticle->publishedAt);

        // Test second article
        $secondArticle = $articles[1];
        $this->assertEquals('Test Article 2', $secondArticle->title);
        $this->assertEquals(['Carlos Hernández'], $secondArticle->authors);
        $this->assertEquals('world news', $secondArticle->categories[0]['name']);
        $this->assertEquals('guardian', $secondArticle->categories[0]['source']);
    }

    public function test_handles_articles_without_byline()
    {
        Http::fake([
            'content.guardianapis.com/search*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'id' => 'test-789',
                            'webTitle' => 'Test Article 3',
                            'webUrl' => 'https://example.com/3',
                            'webPublicationDate' => now()->toIso8601String(),
                            'fields' => [
                                'body' => '<p>No byline content</p>'
                            ],
                            'sectionName' => 'Politics',
                            'type' => 'article'
                        ]
                    ]
                ]
            ])
        ]);

        $client = new GuardianApiClient();
        $articles = $client->fetchArticles(now()->subDay());

        $this->assertCount(1, $articles);
        $this->assertEquals(['Unknown'], $articles[0]->authors);
    }

    public function test_throws_rate_limit_exception()
    {
        Http::fake([
            'content.guardianapis.com/*' => Http::response(
                'Too many requests',
                429,
                ['Retry-After' => '60']
            ),
        ]);

        $this->expectException(ApiRateLimitException::class);
        $this->expectExceptionMessage('API rate limit exceeded');
        $this->expectExceptionCode(429);

        $client = new GuardianApiClient();
        $client->fetchArticles(now()->subHour());
    }
}

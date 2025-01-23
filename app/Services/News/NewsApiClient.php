<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;
use App\Services\News\Contracts\NewsApiClientInterface;
use App\Services\News\DataTransferObjects\ArticleDTO;
use App\Exceptions\ApiException;
use App\Exceptions\ApiRateLimitException;
use Carbon\Carbon;
use GuzzleHttp\Exception\ConnectException;

class NewsApiClient implements NewsApiClientInterface
{
    public function fetchArticles(\DateTime $fromDate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.newsapi.key'),
                'Accept' => 'application/json',
            ])->get('https://newsapi.org/v2/everything', [
                'q' => '*',
                'from' => $fromDate->format('Y-m-d\TH:i:s'),
                'pageSize' => 100,
                'sortBy' => 'publishedAt',
                'language' => 'en'
            ]);

            if ($response->status() === 500) {
                throw new ApiException(
                    'newsapi',
                    'Internal server error: ' . $response->body(),
                    $response->status()
                );
            }

            if ($response->status() === 429) {
                throw new ApiRateLimitException(
                    $response->header('Retry-After', 60)
                );
            }

            if ($response->failed()) {
                throw new ApiException(
                    'newsapi',
                    "API Error: {$response->body()}",
                    $response->status()
                );
            }

            return $this->transformResponse($response->json()['articles'] ?? []);
        } catch (ConnectException $e) {
            throw new ApiException('newsapi', 'Connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function transformResponse(array $apiData): array
    {
        return array_map(function ($article) {
            return new ArticleDTO(
                source: 'newsapi',
                sourceId: md5($article['url']),
                title: $article['title'],
                content: $article['content'] ?? $article['description'] ?? '',
                url: $article['url'],
                publishedAt: Carbon::parse($article['publishedAt']),
                authors: $this->extractAuthors($article['author']),
                categories: [
                    [
                        'name' => strtolower($article['source']['name'] ?? 'General'),
                        'source' => 'newsapi'
                    ]
                ]
            );
        }, $apiData);
    }

    private function extractAuthors(?string $author): array
    {
        if (empty($author)) {
            return ['Unknown'];
        }

        // Split complex author strings with multiple separators
        $authors = preg_split('/\s+and\s+|\s*,\s*/', $author);

        return array_map(function ($authorPart) {
            // Extract only the actual name portion
            $cleanName = preg_replace('/\(.*?\)|\[.*?\]|<.*?>/', '', $authorPart); // Remove titles in brackets
            return trim(substr($cleanName, 0, 255)); // Ensure DB compatibility
        }, $authors);
    }
}

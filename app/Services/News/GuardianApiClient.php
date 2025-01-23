<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;
use App\Services\News\Contracts\NewsApiClientInterface;
use App\Services\News\DataTransferObjects\ArticleDTO;
use App\Exceptions\ApiRateLimitException;
use App\Exceptions\ApiException;
use Carbon\Carbon;
use GuzzleHttp\Exception\ConnectException;

class GuardianApiClient implements NewsApiClientInterface
{
    public function fetchArticles(\DateTime $fromDate): array
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get('https://content.guardianapis.com/search', [
                'api-key' => config('services.guardian.key'),
                'from-date' => $fromDate->format('Y-m-d\TH:i:s'),
                'show-fields' => 'body,byline',
                'page-size' => 50,
                'section' => 'news,technology,science,world',
                'order-by' => 'newest',
                'section' => 'news|technology|science|world|politics'
            ]);

            if ($response->status() === 500) {
                throw new ApiException(
                    'guardian', 
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
                    'guardian',
                    "API Error: {$response->body()}",
                    $response->status()
                );
            }

            $results = $response->json()['response']['results'] ?? [];

            // Filter out liveblogs
            return $this->transformResponse(array_filter($results, function ($article) {
                return $article['type'] === 'article';
            }));
        } catch (ConnectException $e) {
            throw new ApiException('guardian', 'Connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function transformResponse(array $apiData): array
    {
        return array_map(function ($article) {
            $content = $this->extractContent($article['fields']['body'] ?? '');

            return new ArticleDTO(
                source: 'guardian',
                sourceId: $article['id'],
                title: $article['webTitle'],
                content: $content,
                url: $article['webUrl'],
                publishedAt: Carbon::parse($article['webPublicationDate']),
                authors: $this->extractAuthors($article['fields']['byline'] ?? ''),
                categories: [
                    [
                        'name' => strtolower($article['sectionName'] ?? 'General'),
                        'source' => 'guardian'
                    ]
                ]
            );
        }, $apiData);
    }

    private function extractAuthors(?string $byline): array
    {
        if (empty($byline)) {
            return ['Unknown'];
        }

        // Remove "By " prefix and special characters
        $cleaned = preg_replace('/^By /i', '', $byline);

        // Split on multiple separators using regex
        $authorParts = preg_split('/\s*,\s*|\s+and\s+/i', $cleaned);

        return array_filter(array_map(function ($part) {
            // Remove titles/qualifications in parentheses
            $name = preg_replace('/\(.*?\)|\[.*?\]|<.*?>/', '', $part);

            // Clean up remaining special characters
            $name = preg_replace('/[^\p{L}\p{N}\s\-\.]/u', '', $name);

            return trim($name);
        }, $authorParts));
    }

    private function extractContent(string $html): string
    {
        // Convert HTML to plain text and trim
        return trim(strip_tags(str_replace('</p>', "\n\n", $html)));
    }
}

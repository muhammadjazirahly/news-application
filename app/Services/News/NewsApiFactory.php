<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsApiClientInterface;
use InvalidArgumentException;

class NewsApiFactory
{
    public static function make(string $source): NewsApiClientInterface
    {
        return match ($source) {
            'guardian' => app(GuardianApiClient::class),
            'newsapi' => app(NewsApiClient::class),
            default => throw new InvalidArgumentException(
                "Unsupported news source: {$source}"
            ),
        };
    }
}

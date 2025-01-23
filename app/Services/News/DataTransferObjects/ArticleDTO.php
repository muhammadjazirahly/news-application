<?php

namespace App\Services\News\DataTransferObjects;

use Carbon\Carbon;

class ArticleDTO
{
    public function __construct(
        public readonly string $source,
        public readonly string $sourceId,
        public readonly string $title,
        public readonly string $content,
        public readonly string $url,
        public readonly Carbon $publishedAt,
        public readonly array $authors,
        public readonly array $categories
    ) {}
}

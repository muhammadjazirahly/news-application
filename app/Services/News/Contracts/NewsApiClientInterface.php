<?php

namespace App\Services\News\Contracts;

interface NewsApiClientInterface
{
    public function fetchArticles(\DateTime $fromDate): array;
}

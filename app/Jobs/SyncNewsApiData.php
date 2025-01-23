<?php

namespace App\Jobs;

use App\Services\News\NewsApiFactory;
use App\Services\News\NewsSyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncNewsApiData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $apiSource,
        private \DateTime $fromDate
    ) {}

    public function handle(NewsSyncService $syncService): void
    {
        try {
            $client = NewsApiFactory::make($this->apiSource);
            $articles = $client->fetchArticles($this->fromDate);

            foreach ($articles as $articleDTO) {
                $syncService->syncArticle($articleDTO);
            }
        } catch (\Exception $e) {
            Log::error("Sync failed: " . $e->getMessage());
            $this->fail($e);
        }
    }
}

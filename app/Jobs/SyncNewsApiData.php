<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\News\NewsApiFactory;
use App\Services\News\NewsSyncService;
use App\Exceptions\ApiRateLimitException;
use App\Exceptions\ApiException;

class SyncNewsApiData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [60, 300, 1800, 3600, 7200];

    public function __construct(
        private string $apiSource,
        private \DateTime $fromDate
    ) {
        if (!in_array($apiSource, ['guardian', 'newsapi'])) {
            throw new \InvalidArgumentException("Invalid API source: $apiSource");
        }
    }

    public function handle(): void
    {
        try {
            $client = NewsApiFactory::make($this->apiSource);
            $articles = $client->fetchArticles($this->fromDate);

            foreach ($articles as $articleDTO) {
                app(NewsSyncService::class)->syncArticle($articleDTO);
            }
        } catch (ApiRateLimitException $e) {
            Log::channel('sync')->error("API rate limit reached for {$this->apiSource}: " . $e->getMessage());
            $this->release($e->retryAfter);
        } catch (ApiException $e) {
            Log::channel('sync')->error("API error for {$this->apiSource}: " . $e->getMessage());
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts()] ?? 60);
            }
        } catch (\Exception $e) {
            Log::channel('sync_failures')->error("Unexpected error syncing {$this->apiSource}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}

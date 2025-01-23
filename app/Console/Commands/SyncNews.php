<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncNewsApiData;

class SyncNews extends Command
{
    protected $signature = 'sync:news';
    protected $description = 'Sync news articles from external APIs';

    public function handle()
    {
        $sources = ['guardian', 'newsapi'];
        $fromDate = now()->subDays(7);

        foreach ($sources as $source) {
            SyncNewsApiData::dispatch($source, $fromDate)
                ->onQueue('news-sync');

            $this->info("Dispatched sync job for: {$source}");
        }
    }
}

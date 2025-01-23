<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\SyncNewsApiData;
use App\Services\News\NewsSyncService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewsSyncJobTest extends TestCase
{
    use DatabaseTransactions;

    public function test_job_persists_data()
    {
        Http::fake([
            'content.guardianapis.com/search*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'id' => 'test-456',
                            'webTitle' => 'Job Test Article',
                            'webUrl' => 'https://job-test.com',
                            'webPublicationDate' => now()->subHour()->toIso8601String(),
                            'fields' => [
                                'body' => 'Job content',
                                'byline' => 'Test Author'
                            ],
                            'sectionName' => 'Science',
                            'type' => 'article'
                        ]
                    ]
                ]
            ])
        ]);

        $job = new SyncNewsApiData('guardian', now()->subHour());
        $job->handle(app()->make(NewsSyncService::class));

        $this->assertDatabaseHas('articles', [
            'title' => 'Job Test Article',
            'source' => 'guardian',
            'source_id' => 'test-456'
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => 'Test Author'
        ]);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuestApiTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function guest_can_list_articles_with_pagination()
    {
        $totalArticles = Article::count();
        $perPage = 5;

        $response = $this->getJson("/api/articles?per_page=$perPage&page=2");

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ])
            ->assertJson([
                'meta' => [
                    'current_page' => 2,
                    'per_page' => $perPage,
                    'total' => $totalArticles,
                ],
            ]);
    }

    /** @test */
    public function guest_can_filter_articles_by_author()
    {
        $author = Author::first();
        $article = $author->articles()->first();

        $response = $this->getJson("/api/articles?author={$author->name}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $article->id]);
    }

    /** @test */
    public function guest_can_filter_articles_by_category()
    {
        $category = Category::first();
        $article = $category->articles()->first();

        $response = $this->getJson("/api/articles?category={$category->name}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $article->id]);
    }

    /** @test */
    public function guest_can_filter_articles_by_publish_date()
    {
        $article = Article::first();
        $date = date_create($article->published_at)->format('Y-m-d');

        $response = $this->getJson("/api/articles?published_at=$date");

        $response->assertOk()
            ->assertJsonFragment(['id' => $article->id]);
    }

    /** @test */
    public function guest_receives_empty_data_when_no_articles_match()
    {
        $response = $this->getJson('/api/articles?author=NonExistentAuthor');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function guest_receives_validation_errors_for_invalid_params()
    {
        $response = $this->getJson('/api/articles?published_at=invalid-date');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['published_at']);
    }
}

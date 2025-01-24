<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Author;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FavoriteTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $author;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->author = Author::firstOrCreate([
            'name' => 'John Doe',
            'source' => 'guardian',
            'source_id' => '123',
        ]);

        $this->category = Category::firstOrCreate([
            'name' => 'Tech',
            'source' => 'guardian',
        ]);

        // Authenticate the user
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function user_can_toggle_favorite_author()
    {
        // Mark author as favorite
        $response = $this->postJson("/api/favorite/authors/{$this->author->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Favorite author toggled successfully.']);

        // Refresh the user relationship
        $this->user->refresh();

        // Check if the author is favorited
        $this->assertTrue($this->user->favoriteAuthors->contains($this->author));

        // Unmark author as favorite
        $response = $this->postJson("/api/favorite/authors/{$this->author->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Favorite author toggled successfully.']);

        // Refresh the user relationship
        $this->user->refresh();

        // Check if the author is unfavorited
        $this->assertFalse($this->user->favoriteAuthors->contains($this->author));
    }

    /** @test */
    public function user_can_get_favorite_authors()
    {
        $this->user->favoriteAuthors()->attach($this->author->id);

        $response = $this->getJson('/api/favorite/authors');
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'source', 'source_id', 'created_at', 'updated_at']
            ]);
    }

    /** @test */
    public function user_can_toggle_favorite_category()
    {
        // Mark category as favorite
        $response = $this->postJson("/api/favorite/categories/{$this->category->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Favorite category toggled successfully.']);

        // Refresh the user relationship
        $this->user->refresh();

        // Check if the category is favorited
        $this->assertTrue($this->user->favoriteCategories->contains($this->category));

        // Unmark category as favorite
        $response = $this->postJson("/api/favorite/categories/{$this->category->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Favorite category toggled successfully.']);

        // Refresh the user relationship
        $this->user->refresh();

        // Check if the category is unfavorited
        $this->assertFalse($this->user->favoriteCategories->contains($this->category));
    }

    /** @test */
    public function user_can_get_favorite_categories()
    {
        $this->user->favoriteCategories()->attach($this->category->id);

        $response = $this->getJson('/api/favorite/categories');
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'source', 'created_at', 'updated_at']
            ]);
    }

    /** @test */
    public function user_can_get_personalized_articles()
    {
        $article = Article::firstOrCreate([
            'title' => 'Test Article',
            'content' => 'Test content',
            'url' => 'https://example.com',
            'published_at' => now(),
            'source' => 'guardian',
            'source_id' => '123',
        ]);

        // Associate the article with the author and category
        $article->authors()->syncWithoutDetaching([$this->author->id]);
        $article->categories()->syncWithoutDetaching([$this->category->id]);

        $this->user->favoriteAuthors()->syncWithoutDetaching([$this->author->id]);
        $this->user->favoriteCategories()->syncWithoutDetaching([$this->category->id]);

        $response = $this->getJson('/api/favorite/articles');
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'content', 'url', 'published_at', 'source', 'source_id', 'created_at', 'updated_at']
            ]);
    }
}

<?php

namespace App\Providers;

use App\Services\News\NewsApiClient;
use App\Repositories\AuthorRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Services\News\GuardianApiClient;
use App\Repositories\FavoriteRepository;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class NewsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind API Clients
        $this->app->bind(GuardianApiClient::class, function () {
            return new GuardianApiClient();
        });

        $this->app->bind(NewsApiClient::class, function () {
            return new NewsApiClient();
        });

        // Bind Repositories
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
    }
}

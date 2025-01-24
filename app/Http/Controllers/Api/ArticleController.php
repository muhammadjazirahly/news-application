<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ArticleRepository;
use App\Http\Requests\ArticlesRequest;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index(ArticlesRequest $request): ArticleCollection
    {
        $validatedRequest = $request->validated();
  
        $articles = $this->articleRepository->filter($validatedRequest);
      
        return new ArticleCollection($articles);
    }
}
<?php

namespace App\Repositories\Contracts;

use App\Models\Author;

interface AuthorRepositoryInterface
{
    public function firstOrCreate(array $attributes): Author;
}

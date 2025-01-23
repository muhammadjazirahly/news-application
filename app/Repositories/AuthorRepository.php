<?php

namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryInterface;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function firstOrCreate(array $attributes): Author
    {
        return Author::firstOrCreate(
            ['name' => $attributes['name'], 'source' => $attributes['source']],
            $attributes
        );
    }
}

<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function firstOrCreate(array $attributes): Category
    {
        return Category::firstOrCreate(
            ['name' => $attributes['name'], 'source' => $attributes['source']],
            $attributes
        );
    }
}

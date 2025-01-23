<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'source'];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_favorite_categories');
    }
}

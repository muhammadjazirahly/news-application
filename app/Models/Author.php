<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'source', 'source_id'];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_favorite_authors');
    }
}

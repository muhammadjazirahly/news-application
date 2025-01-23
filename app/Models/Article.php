<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 
        'content', 
        'url', 
        'published_at', 
        'source', 
        'source_id'
    ];

    protected $dates = ['published_at'];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}

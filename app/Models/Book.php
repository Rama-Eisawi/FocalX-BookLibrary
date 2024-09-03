<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'author', 'description', 'published_at'];

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    /**
     * Scope a query to only include books by a given author.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $author
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAuthor($query, $author)
    {
        return $query->where('author', 'like', '%' . $author . '%');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function scopeAvailableForBorrowing($query)
    {
        return $query->whereDoesntHave('borrowRecords', function ($q) {
            $q->whereNull('returned_at');
        });
    }
}

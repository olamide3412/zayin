<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'body',
        'cover_image', 'media_gallery', 'category', 'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'media_gallery' => 'array',
    ];

    /**
     * Auto-generate slug from title.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($news) {
            $news->slug = $news->slug ?: Str::slug($news->title) . '-' . time();
        });
        static::updating(function ($news) {
            if ($news->isDirty('title') && !$news->isDirty('slug')) {
                $news->slug = Str::slug($news->title) . '-' . time();
            }
        });
    }

    /**
     * Author relationship.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope: only published news.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderByDesc('published_at');
    }
}

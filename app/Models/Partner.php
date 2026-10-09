<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url',
        'website_url',
        'description',
        'status',
        'sort_order',
    ];

    /**
     * Scope active partners ordered by sort_order then newest.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order')->orderByDesc('id');
    }
}

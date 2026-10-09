<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    use HasFactory;

    /**
     * Explicit table name — Laravel would default to 'testimonies' (incorrect).
     */
    protected $table = 'testimonials';

    protected $fillable = [
        'name', 'occupation', 'rating', 'message', 'status',
    ];

    /**
     * Scope: only approved testimonials.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->orderByDesc('created_at');
    }

    /**
     * Scope: pending testimonials.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')->orderByDesc('created_at');
    }
}

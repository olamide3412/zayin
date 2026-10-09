<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectVideo extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'transcript',
        'source',
        'external_url',
        'video_path',
        'status',
        'duration_seconds',
        'poster_path',
        'renditions',
        'file_size',
        'sort_order',
    ];

    protected $casts = [
        'renditions' => 'array',
        'duration_seconds' => 'integer',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

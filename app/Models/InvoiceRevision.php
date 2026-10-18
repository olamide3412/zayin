<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceRevision extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $appends = [
        'snapshot_data',
    ];

    protected $fillable = [
        'invoice_id',
        'user_id',
        'before',
        'after',
        'reason',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSnapshotDataAttribute(): array
    {
        return $this->after ?? ($this->before ?? []);
    }

    public function getRevisionNumberAttribute(): int
    {
        return ($this->after['revision_number'] ?? null)
            ?: (int) ($this->attributes['id'] ?? 1);
    }
}

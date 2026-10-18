<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceItemCatalog extends Model
{
    use HasFactory;

    protected $table = 'invoice_items_catalog';

    protected $fillable = [
        'business_id',
        'name',
        'normalized_name',
        'description',
        'unit',
        'default_price_kobo',
        'last_price_kobo',
        'is_vatable',
        'category',
        'usage_count',
        'last_used_at',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'default_price_kobo' => 'integer',
            'last_price_kobo' => 'integer',
            'is_vatable' => 'boolean',
            'usage_count' => 'integer',
            'last_used_at' => 'datetime',
            'is_archived' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'catalog_item_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    /**
     * Normalize a name string for deduplication (lowercase, trim, collapsed spaces, no punctuation).
     */
    public static function normalizeName(string $name): string
    {
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', '', $name);
        $collapsed = preg_replace('/\s+/', ' ', trim($cleaned ?? ''));
        return mb_strtolower($collapsed);
    }
}

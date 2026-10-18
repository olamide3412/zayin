<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'catalog_item_id',
        'sort_order',
        'item_name',
        'description',
        'quantity',
        'unit',
        'unit_price_kobo',
        'discount_type',
        'discount_value',
        'is_vatable',
        'line_net_kobo',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_price_kobo' => 'integer',
            'discount_value' => 'decimal:2',
            'is_vatable' => 'boolean',
            'line_net_kobo' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItemCatalog::class, 'catalog_item_id');
    }

    public function getNameAttribute(): string
    {
        return $this->attributes['item_name'] 
            ?? $this->attributes['description'] 
            ?? '';
    }

    public function setNameAttribute($val): void
    {
        $this->attributes['item_name'] = $val;
    }

    public function getTotalKoboAttribute(): int
    {
        return (int) ($this->attributes['line_net_kobo'] ?? 0);
    }

    public function setTotalKoboAttribute($val): void
    {
        $this->attributes['line_net_kobo'] = (int) $val;
    }

    public function getTaxableAttribute(): bool
    {
        return (bool) ($this->attributes['is_vatable'] ?? false);
    }

    public function setTaxableAttribute($val): void
    {
        $this->attributes['is_vatable'] = (bool) $val;
    }

    public function getDiscountKoboAttribute(): int
    {
        $gross = (int) round(($this->quantity ?? 1) * ($this->unit_price_kobo ?? 0));
        return max(0, $gross - $this->total_kobo);
    }
}

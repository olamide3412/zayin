<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name_snapshot',
        'sku_snapshot',
        'qty',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'integer',
        'total' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return format_naira($this->total);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return format_naira($this->unit_price);
    }
}

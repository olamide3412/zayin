<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'number',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'status',
        'payment_status',
        'fulfilment_type',
        'subtotal',
        'delivery_fee',
        'install_fee',
        'discount',
        'tax',
        'total',
        'amount_paid',
        'currency',
        'state_id',
        'city',
        'address',
        'landmark',
        'shipping_address',
        'notes',
        'placed_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'delivery_fee' => 'integer',
        'install_fee' => 'integer',
        'discount' => 'integer',
        'tax' => 'integer',
        'total' => 'integer',
        'amount_paid' => 'integer',
        'shipping_address' => 'array',
        'placed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return format_naira($this->total);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return format_naira($this->subtotal);
    }

    public function getFormattedDeliveryFeeAttribute(): string
    {
        return format_naira($this->delivery_fee);
    }

    public function getFormattedInstallFeeAttribute(): string
    {
        return format_naira($this->install_fee);
    }
}

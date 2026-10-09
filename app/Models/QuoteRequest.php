<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'name',
        'email',
        'phone',
        'service_id',
        'product_id',
        'service_type',
        'property_type',
        'state_id',
        'city',
        'address',
        'budget_range',
        'appliances',
        'calculation_id',
        'calculation_uuid',
        'details',
        'preferred_contact',
        'status',
        'quoted_amount',
        'notes',
    ];

    protected $casts = [
        'appliances' => 'array',
        'quoted_amount' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculation(): BelongsTo
    {
        return $this->belongsTo(SolarCalculation::class, 'calculation_id');
    }
}

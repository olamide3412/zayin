<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount_kobo',
        'method',
        'reference',
        'paid_on',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount_kobo' => 'integer',
            'paid_on' => 'date',
        ];
    }

    public function getPaidAtAttribute()
    {
        return $this->paid_on;
    }

    public function setPaidAtAttribute($value)
    {
        $this->attributes['paid_on'] = $value;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_VOID = 'void';

    protected $fillable = [
        'business_id',
        'customer_id',
        'template_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'payment_terms',
        'reference',
        'subject',
        'vat_mode',
        'vat_rate_bps',
        'invoice_discount_kobo',
        'subtotal_kobo',
        'discount_kobo',
        'vat_kobo',
        'total_kobo',
        'paid_kobo',
        'amount_paid_kobo',
        'balance_kobo',
        'amount_in_words',
        'notes',
        'terms',
        'bank_account_ids',
        'issued_at',
        'voided_at',
        'void_reason',
        'created_by',
        'public_token',
        'irn',
        'qr_payload',
    ];

    protected $appends = [
        'amount_paid_kobo',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'vat_rate_bps' => 'integer',
            'invoice_discount_kobo' => 'integer',
            'subtotal_kobo' => 'integer',
            'discount_kobo' => 'integer',
            'vat_kobo' => 'integer',
            'total_kobo' => 'integer',
            'paid_kobo' => 'integer',
            'balance_kobo' => 'integer',
            'bank_account_ids' => 'array',
            'issued_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->public_token)) {
                $invoice->public_token = Str::random(40);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InvoiceTemplate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->orderByDesc('paid_on');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(InvoiceRevision::class)->orderByDesc('created_at');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isIssued(): bool
    {
        return in_array($this->status, ['issued', 'partially_paid', 'paid', 'overdue']);
    }

    public function isVoid(): bool
    {
        return $this->status === 'void';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' || ($this->total_kobo > 0 && $this->balance_kobo <= 0);
    }

    public function getAmountPaidKoboAttribute(): int
    {
        return (int) ($this->attributes['paid_kobo'] ?? 0);
    }

    public function setAmountPaidKoboAttribute($value): void
    {
        $this->attributes['paid_kobo'] = (int) $value;
        $this->attributes['balance_kobo'] = max(0, ($this->total_kobo ?? 0) - (int) $value);
    }

    public function getRevisionNumberAttribute(): int
    {
        return $this->revisions()->count() + 1;
    }

    public function getCustomerSnapshotAttribute(): array
    {
        if ($this->customer) {
            return [
                'name' => $this->customer->name,
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'address' => $this->customer->address,
                'tin' => $this->customer->tin,
            ];
        }
        return [];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', 'void');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'layout',
        'accent_color',
        'font_family',
        'logo_position',
        'document_title',
        'show_fields',
        'show_watermark',
        'number_prefix',
        'number_padding',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'show_fields' => 'array',
            'show_watermark' => 'boolean',
            'is_default' => 'boolean',
            'number_padding' => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the default invoice template for a business.
     */
    public static function getDefault(?int $businessId = null): self
    {
        $query = self::query();
        if ($businessId) {
            $query->where('business_id', $businessId);
        }
        return $query->where('is_default', true)->first()
            ?? $query->first()
            ?? new self([
                'name' => 'Classic Professional',
                'layout' => 'classic',
                'accent_color' => '#0F172A',
                'is_default' => true,
            ]);
    }

    /**
     * Get default field visibility configuration.
     */
    public static function defaultShowFields(): array
    {
        return [
            'rc_number' => true,
            'tin' => true,
            'website' => true,
            'po_reference' => true,
            'unit_column' => true,
            'discount_column' => true,
            'vat_column' => true,
            'amount_in_words' => true,
            'bank_details' => true,
            'signature' => true,
            'stamp' => false,
        ];
    }

    /**
     * Determine if a field should be shown on the invoice.
     */
    public function showField(string $fieldName, bool $default = true): bool
    {
        $fields = $this->show_fields ?? self::defaultShowFields();
        return (bool) ($fields[$fieldName] ?? $default);
    }
}

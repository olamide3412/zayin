<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tagline',
        'logo_path',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'phone',
        'phone_alt',
        'email',
        'website',
        'rc_number',
        'tin',
        'signature_path',
        'stamp_path',
        'vat_rate_bps',
        'default_vat_mode',
        'default_terms',
        'default_notes',
        'footer_text',
    ];

    protected function casts(): array
    {
        return [
            'vat_rate_bps' => 'integer',
        ];
    }

    /**
     * Get or create the primary active business profile.
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Zayin Global Limited',
                'tagline' => 'Solar Energy, Starlink & DSTV Installations',
                'address_line1' => 'Abraka, Ethiope East',
                'address_line2' => 'Delta State, Nigeria',
                'city' => 'Abraka',
                'state' => 'Delta',
                'phone' => '+234 803 123 4567',
                'email' => 'info@zayin.ng',
                'website' => 'https://zayin.ng',
                'rc_number' => 'RC-1849204',
                'tin' => '24819204-0001',
                'vat_rate_bps' => 750,
                'default_vat_mode' => 'exclusive',
                'default_terms' => '1. Payment is due according to specified terms.\n2. Goods remain property of Zayin Global Limited until paid in full.\n3. Warranties cover certified equipment and standard workmanship.',
                'default_notes' => 'Thank you for choosing Zayin Global Limited for your clean power and connectivity needs.',
                'footer_text' => 'Thank you for your business. Certified Engineering & Nationwide Deployments.',
            ]
        );
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BusinessBankAccount::class)->orderBy('sort_order');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(InvoiceTemplate::class);
    }

    public function defaultTemplate(): HasOne
    {
        return $this->hasOne(InvoiceTemplate::class)->where('is_default', true);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(InvoiceItemCatalog::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function numberSequences(): HasMany
    {
        return $this->hasMany(InvoiceNumberSequence::class);
    }
}

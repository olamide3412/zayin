<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'service_id',
        'type',
        'name',
        'slug',
        'sku',
        'summary',
        'description',
        'price',
        'compare_at_price',
        'price_label',
        'requires_quote',
        'installation_included',
        'warranty_text',
        'specs',
        'brand',
        'image',
        'gallery',
        'stock_qty',
        'track_stock',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'price' => 'integer',
        'compare_at_price' => 'integer',
        'requires_quote' => 'boolean',
        'installation_included' => 'boolean',
        'track_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'specs' => 'array',
        'gallery' => 'array',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function inclusions(): HasMany
    {
        return $this->hasMany(ProductInclusion::class, 'product_id')->orderBy('sort_order');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_product');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return format_naira($this->price);
    }

    public function getFormattedComparePriceAttribute(): ?string
    {
        return $this->compare_at_price ? format_naira($this->compare_at_price) : null;
    }
}

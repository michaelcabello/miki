<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_template_id',
        'sku',
        'barcode',
        'price_sale',
        'price_wholesale',
        'price_purchase',
        'active',
        'is_default',
        'combination_key',
        'variant_name',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class, 'product_template_id');
    }

    public function values(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'attribute_value_product_variant', // pivot
            'product_variant_id',
            'attribute_value_id'
        )->withTimestamps();
    }
}

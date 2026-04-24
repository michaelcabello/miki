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

    /* public function productTemplate()
    {
        return $this->belongsTo(ProductTemplate::class);
    } */



    // Una variante puede tener muchas imágenes (galería)
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id')->orderBy('sort_order')->orderBy('id');
    }

    // Relación de conveniencia para obtener solo la imagen principal
    /* public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_variant_id')->where('is_main', true);
    } */

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_variant_id')
            ->where('is_primary', true);
    }

    /**
     * Relación: La variante pertenece a una plantilla (Odoo Style)
     */
    public function productTemplate(): BelongsTo
    {
        // Asegúrate de que la llave foránea en tu migración sea 'product_template_id'
        return $this->belongsTo(ProductTemplate::class, 'product_template_id');
    }

    //para obtener la cantidad de productos
    public function getStockActualAttribute()
    {
        // Suma las cantidades de todas las ubicaciones de tipo 'internal'
        return $this->quants()->sum('qty');
    }
}

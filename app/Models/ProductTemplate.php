<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'active',
        'type',
        'sale_ok',
        'purchase_ok',
        'pos_ok',
        'active',
        'uom_id',
        'uom_po_id',
        'category_id',
    ];



    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function purchaseUom()
    {
        return $this->belongsTo(Uom::class, 'uom_po_id');
    }

    public function packagings()
    {
        return $this->hasMany(ProductPackaging::class);
    }



    public function attributeLines()
    {
        return $this->hasMany(\App\Models\ProductTemplateAttribute::class);
    }



    //public function defaultVariant()
    public function nonDefaultVariants()
    {
        return $this->hasOne(ProductVariant::class)
            ->where('is_default', 0);
    }

    // app/Models/ProductTemplate.php
    public function detraction()
    {
        return $this->belongsTo(Detraction::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // Relaciones con impuestos de venta y compra
    //para que los productos puedan tener varios impuestos
    public function saleTaxes()
    {
        return $this->belongsToMany(Tax::class, 'product_template_sale_taxes')
            ->withPivot('sequence')
            ->withTimestamps()
            ->orderBy('pivot_sequence');
    }
    // Relaciones con impuestos de venta y compra
    public function purchaseTaxes()
    {
        return $this->belongsToMany(Tax::class, 'product_template_purchase_taxes')
            ->withPivot('sequence')
            ->withTimestamps()
            ->orderBy('pivot_sequence');
    }


    public function defaultPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }




    // Relación "Has Many Through" para obtener todas las imágenes de todas sus variantes
    /* public function images()
    {
        return $this->hasManyThrough(
            ProductImage::class,
            ProductVariant::class,
            'product_template_id',
            'product_variant_id'
        );
    } */

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Obtiene la variante por defecto (la que no tiene atributos o es marcada como principal)
     */
    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class, 'product_template_id')
            ->where('is_default', true)
            ->withDefault(); // Evita errores si no existe
    }
}

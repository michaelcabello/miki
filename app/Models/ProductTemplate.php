<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductTemplate extends Model
{
    protected $fillable = [
        // --- Información Básica & Identificación ---
        'name',
        'slug',
        'type', // goods, service, combo
        'active',

        // --- Control de Visibilidad (Flags) ---
        'sale_ok',
        'purchase_ok',
        'pos_ok',

        // --- Categorización y Unidades ---
        'category_id',
        'uom_id',    // Unidad de medida base
        'uom_po_id', // Unidad de medida de compra (Purchase Order)

        // --- Marca y Clasificación ---
        'brand_id',
        'modello_id',
        'season_id',

        // --- Contenido Web & Descripciones ---
        'short_description',
        'long_description',

        // --- Optimización SEO (Google) ---
        'title_google',
        'description_google',
        'keywords_google',

        // --- Configuración de Suscripciones ---
        'is_recurring',
        'subscription_plan_id',
        'recurring_price', // Si decidiste guardarlo en el template

        // --- Contabilidad e Impuestos ---
        'account_buy_id',
        'account_sell_id',
        'detraction_id',
    ];



    protected $casts = [
        'active'       => 'boolean',
        'sale_ok'      => 'boolean',
        'purchase_ok'  => 'boolean',
        'pos_ok'       => 'boolean',
        'is_recurring' => 'boolean',
        // Si manejas precios decimales, asegúrate de esto:
        'recurring_price' => 'decimal:2',
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


    //agregando relaciones con categorypost y adicional de productos

    public function posCategories()
    {
        return $this->belongsToMany(
            \App\Models\PosCategory::class,
            'pos_category_product_template'
        )->withTimestamps();
    }

    public function additionalProducts()
    {
        return $this->belongsToMany(
            self::class,
            'product_template_additional_products',
            'product_template_id',
            'additional_product_template_id'
        )->withPivot(['sequence', 'active'])
            ->withTimestamps();
    }

    public function parentAdditionalProducts()
    {
        return $this->belongsToMany(
            self::class,
            'product_template_additional_products',
            'additional_product_template_id',
            'product_template_id'
        )->withPivot(['sequence', 'active'])
            ->withTimestamps();
    }

    // Brochures activos y ordenados
    public function brochures()
    {
        return $this->hasMany(ProductBrochure::class)
            ->where('state', true)
            ->orderBy('order', 'asc');
    }

    // Videos activos y ordenados
    public function videos()
    {
        return $this->hasMany(ProductVideo::class)
            ->where('state', true)
            ->orderBy('order', 'asc');
    }
}

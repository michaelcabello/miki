<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'sale_ok',
        'purchase_ok',
        'pos_ok',
        'active',

        // Unidades de medida
        'uom_id',
        'uom_po_id',

        // Clasificación
        'category_id',
        'brand_id',
        'modello_id',
        'season_id',
        'detraction_id',

        // Cuentas contables
        'account_sell_id',
        'account_buy_id',

        // Inventario
        'tracking',

        // Suscripciones
        'is_recurring',
        'subscription_plan_id',
        'recurring_price',

        // Web / SEO
        'short_description',
        'long_description',
        'title_google',
        'description_google',
        'keywords_google',
    ];


    protected $casts = [
        'active'       => 'boolean',
        'sale_ok'      => 'boolean',
        'purchase_ok'  => 'boolean',
        'pos_ok'       => 'boolean',
        'is_recurring' => 'boolean',
        // Si manejas precios decimales, asegúrate de esto:
        //'recurring_price' => 'decimal:2',
    ];



    // =========================================================================
    // RELACIONES BelongsTo (FK directas en product_templates)
    // =========================================================================
    /**
     * Relación: Unidad de Medida Principal (uom_id)
     */
    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    /**
     * Relación: Unidad de Medida de Compra (uom_po_id)
     * Esta es la que te está dando el error en el eager loading
     */
    //
    public function purchaseUom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_po_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function modello(): BelongsTo
    {
        return $this->belongsTo(Modello::class, 'modello_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function detraction(): BelongsTo
    {
        return $this->belongsTo(Detraction::class, 'detraction_id');
    }

    public function accountSell(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_sell_id');
    }

    public function accountBuy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_buy_id');
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }


 // =========================================================================
    // RELACIONES HasMany / HasOne
    // =========================================================================
    /**
     * Relación: Una plantilla tiene muchas variantes
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_template_id');
    }
    /**
     * Obtiene la variante por defecto (la que no tiene atributos o es marcada como principal)
     */
    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class, 'product_template_id')
            ->where('is_default', true);
    }

    public function attributeLines(): HasMany
    {
        return $this->hasMany(ProductTemplateAttribute::class, 'product_template_id');
    }

    public function packagings(): HasMany
    {
        return $this->hasMany(ProductPackaging::class, 'product_template_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_template_id');
    }

    public function pricelistItems(): HasMany
    {
        return $this->hasMany(PricelistItem::class, 'product_template_id');
    }


   // =========================================================================
    // RELACIONES BelongsToMany (tablas pivot)
    // =========================================================================

    /**
     * Impuestos de VENTA.
     *
     * Tabla pivot: product_template_sale_taxes
     * Columnas:    product_template_id | tax_id | sequence
     *
     * ⚠️ IMPORTANTE: NO usar orderByPivot('sequence') aquí.
     *    orderByPivot genera "ORDER BY pivot_sequence" que falla en pluck().
     *    Usamos orderBy con el nombre real de la columna en la tabla pivot.
     */
    public function saleTaxes(): BelongsToMany
    {
        return $this->belongsToMany(
            Tax::class,
            'product_template_sale_taxes', // tabla pivot
            'product_template_id',          // FK de este modelo
            'tax_id'                        // FK del modelo relacionado
        )
            ->withPivot('sequence')
            ->orderBy('product_template_sale_taxes.sequence', 'asc'); // ✅ columna real, sin alias
    }

    /**
     * Impuestos de COMPRA.
     *
     * Tabla pivot: product_template_purchase_taxes
     */
    public function purchaseTaxes(): BelongsToMany
    {
        return $this->belongsToMany(
            Tax::class,
            'product_template_purchase_taxes',
            'product_template_id',
            'tax_id'
        )
            ->withPivot('sequence')
            ->orderBy('product_template_purchase_taxes.sequence', 'asc'); // ✅ columna real
    }

    /**
     * Categorías del Punto de Venta (POS).
     *
     * Tabla pivot: pos_category_product_template
     */
    public function posCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            PosCategory::class,
            'pos_category_product_template', // tabla pivot
            'product_template_id',
            'pos_category_id'
        );
    }

    /**
     * Productos adicionales (cross-sell en POS).
     *
     * Tabla pivot: product_template_additional_products
     * Columnas extra: sequence, active
     */
    public function additionalProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductTemplate::class,
            'product_template_additional_products', // tabla pivot
            'product_template_id',                  // FK de este modelo (el "principal")
            'additional_product_template_id'        // FK del modelo relacionado (el adicional)
        )
            ->withPivot('sequence', 'active')
            ->orderBy('product_template_additional_products.sequence', 'asc'); // ✅ columna real
    }




    //public function defaultVariant()
    public function nonDefaultVariants()
    {
        return $this->hasOne(ProductVariant::class)
            ->where('is_default', 0);
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

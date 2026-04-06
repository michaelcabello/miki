<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'amount_type',
        'type_tax_use',
        'tax_scope',
        'sequence',
        'company_id',
        'tax_group_id',
        'cash_basis_transition_account_id',
        'price_include',
        'include_base_amount',
        'is_base_affected',
        'active',
        'description',
    ];

    /* public function accounts()
    {
        return $this->hasMany(Account::class);
    } */


    // Conversión automática de tipos
    protected $casts = [
        'amount'              => 'float',
        'sequence'            => 'integer',
        'price_include'       => 'boolean',
        'include_base_amount' => 'boolean',
        'is_base_affected'    => 'boolean',
        'active'              => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────

    /**
     * Cuenta contable asociada al impuesto.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Líneas de distribución del impuesto (tax repartition lines).
     */
    public function taxRepartitionLines(): HasMany
    {
        return $this->hasMany(TaxRepartitionLine::class);
    }

    /**
     * Plantillas de producto que usan este impuesto en ventas.
     */
    public function productTemplateSaleTaxes(): BelongsToMany
    {
        return $this->belongsToMany(ProductTemplate::class, 'product_template_sale_taxes');
    }

    /**
     * Plantillas de producto que usan este impuesto en compras.
     */
    public function productTemplatePurchaseTaxes(): BelongsToMany
    {
        return $this->belongsToMany(ProductTemplate::class, 'product_template_purchase_taxes');
    }
}

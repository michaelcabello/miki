<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class PurchaseOrderLine extends Model
{
    protected $table = 'purchase_order_lines';

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_uom_id',
        'name',
        'product_qty',
        'qty_received',
        'qty_invoiced',
        'price_unit',
        'price_subtotal',
        'price_total',
        'account_id',
    ];

    /* --- Relaciones --- */

    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'product_uom_id');
    }

    // 🚀 Relación pivot para impuestos por línea
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'purchase_order_line_taxes', 'purchase_order_line_id', 'tax_id');
    }
}

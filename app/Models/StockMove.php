<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMove extends Model
{
    protected $fillable = [
        'stock_picking_id',
        'product_variant_id',
        'location_from_id',
        'location_to_id',
        'purchase_order_line_id',
        'qty_demand',
        'qty_done',
        'price_unit',
        'state'
    ];

    protected $casts = [
        'qty_demand' => 'float',
        'qty_done'   => 'float',
        'price_unit' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones con Nombres Explícitos
    |--------------------------------------------------------------------------
    */

    public function picking(): BelongsTo
    {
        return $this->belongsTo(StockPicking::class, 'stock_picking_id');
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function locationFrom(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_from_id');
    }

    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_to_id');
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class, 'purchase_order_line_id');
    }
}

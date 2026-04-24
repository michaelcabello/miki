<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockQuant extends Model
{
    // 🚀 Ajustado exactamente a tu migración
    protected $fillable = [
        'product_variant_id',
        'location_id',
        'lot_id',
        'quantity',
        'reserved_quantity',
        'last_count_date'
    ];

    protected $casts = [
        'quantity'          => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
        'last_count_date'   => 'datetime',
    ];

    /* --- Relaciones --- */

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }
}

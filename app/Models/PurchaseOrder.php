<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'name',
        'partner_id',
        'currency_id',
        'warehouse_id',
        'picking_type_id',
        'user_id',
        'date_order',
        'date_planned',
        'amount_untaxed',
        'amount_tax',
        'amount_total',
        'currency_rate',
        'state',
        'notes',
        'pdf_path',
    ];

    // Casts para fechas (Laravel 12 style)
    protected function casts(): array
    {
        return [
            'date_order' => 'datetime',
            'date_planned' => 'datetime',
            'amount_total' => 'decimal:4',
            'currency_rate' => 'decimal:6',
        ];
    }

    /* --- Relaciones Odoo Style --- */

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use App\Traits\HasSequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    use HasSequence;

    protected $appends = ['picking_count', 'bill_count'];
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
        'date_approve',
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


    public function pickings(): HasMany
    {
        // Relación con Recepciones (Inventario)
        return $this->hasMany(StockPicking::class, 'purchase_order_id');
    }

    public function accountMoves(): HasMany
    {
        // Relación con Facturas de Proveedor (Vendor Bills)
        return $this->hasMany(AccountMove::class, 'purchase_order_id');
    }

    // Atributos para los contadores de los botones
    public function getPickingCountAttribute(): int
    {
        return $this->pickings()->count();
    }

    public function getBillCountAttribute(): int
    {
        return $this->accountMoves()->count();
    }


    // En App\Models\PurchaseOrder.php
    public function convertToPurchaseOrder()
    {
        if (str_contains($this->name, 'RFQ')) {
            $this->name = str_replace('RFQ', 'P', $this->name);
            // 🚀 IMPORTANTE: No le des save() aquí, deja que el Service lo haga.
        }
    }
}

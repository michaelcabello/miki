<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockPicking extends Model
{
    // 🚀 Definimos los campos que se pueden llenar masivamente
    protected $fillable = [
        'name',
        'operation_type_id',
        'location_from_id',
        'location_to_id',
        'partner_id',
        'purchase_order_id',
        'sale_order_id',
        'state',
        'scheduled_date',
        'date_done',
        'vehicle_plate',
        'note'
    ];

    // 🕒 Conversión automática de fechas para PHP
    protected $casts = [
        'scheduled_date' => 'date',
        'date_done' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones (Arquitectura Odoo)
    |--------------------------------------------------------------------------
    */

    // El socio (Proveedor o Cliente)
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    // La Orden de Compra que originó esta recepción
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    // El tipo de operación (Ej: Recepciones, Entregas, Transferencias Internas)
    public function operationType(): BelongsTo
    {
        return $this->belongsTo(StockOperationType::class, 'operation_type_id');
    }

    // Ubicaciones
    public function locationFrom(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_from_id');
    }

    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_to_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica de Negocio (Estados)
    |--------------------------------------------------------------------------
    */

    /**
     * Helper para identificar el color del badge en la interfaz.
     */
    public function getStateColorAttribute(): string
    {
        return match ($this->state) {
            'draft'     => 'bg-gray-100 text-gray-600',
            'confirmed' => 'bg-blue-100 text-blue-600',
            'assigned'  => 'bg-purple-100 text-purple-600', // Listo para mover
            'done'      => 'bg-green-100 text-green-600',
            'cancel'    => 'bg-red-100 text-red-600',
            default     => 'bg-gray-100 text-gray-600',
        };
    }



    public function moveLines(): HasMany
    {
        // 🚀 CAMBIO: Debe coincidir con el nombre de la columna en la migración de stock_moves
        return $this->hasMany(StockMove::class, 'stock_picking_id');
    }
}

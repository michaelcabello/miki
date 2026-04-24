<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'address',
        'is_main',
        'order',
        'state',
        'lot_stock_id',// NO está aquí: se asigna programáticamente
        // después de crear las ubicaciones del almacén (igual que Odoo)
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'state'   => 'boolean',
        'order'   => 'integer',
    ];

    // ─── RELACIONES ─────────────────────────────────────────────

    /**
     * Ubicaciones físicas que pertenecen a este almacén.
     * Se usan para validar integridad antes de eliminar.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(WarehouseLocation::class);
    }
}

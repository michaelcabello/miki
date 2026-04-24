<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseLocation extends Model
{
    use SoftDeletes;

    protected $table = 'warehouse_locations';

    protected $fillable = [
        'code',
        'name',
        'complete_name',
        'order',
        'parent_id',
        'warehouse_id',
        'usage',
        'scrap_location',
        'state',
        'capacity',
    ];

    protected $casts = [
        'scrap_location' => 'boolean',
        'state'          => 'boolean',
        'order'          => 'integer',
        'capacity'       => 'decimal:2',
    ];

    // ─── CATÁLOGOS DEL ENUM usage ────────────────────────────────

    /**
     * Etiquetas en español para el campo usage (estilo Odoo)
     */
    public static array $usageLabels = [
        'view'       => 'Vista (Agrupador)',
        'internal'   => 'Interno',
        'supplier'   => 'Proveedor',
        'customer'   => 'Cliente',
        'inventory'  => 'Ajuste de Inventario',
        'production' => 'Producción',
        'transit'    => 'Tránsito',
    ];

    /**
     * Clases Tailwind para badge de usage en la lista
     */
    public static array $usageColors = [
        'view'       => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'internal'   => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        'supplier'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'customer'   => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'inventory'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'production' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        'transit'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
    ];

    // ─── RELACIONES ─────────────────────────────────────────────

    /**
     * Almacén al que pertenece esta ubicación
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Ubicación padre (árbol recursivo Odoo-style)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_id');
    }

    /**
     * Ubicaciones hijas directas
     */
    public function children(): HasMany
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_id');
    }

    // ─── LÓGICA DE NEGOCIO ──────────────────────────────────────

    /**
     * Genera el complete_name siguiendo el patrón Odoo: Parent / Name
     * Llamar desde Form Object después de guardar parent_id y name.
     */
    public function generateCompleteName(): string
    {
        if ($this->parent_id && $this->relationLoaded('parent') && $this->parent) {
            $parentName = $this->parent->complete_name ?? $this->parent->name;
            return $parentName . ' / ' . $this->name;
        }

        if ($this->warehouse_id && $this->relationLoaded('warehouse') && $this->warehouse) {
            return $this->warehouse->code . ' / ' . $this->name;
        }

        return $this->name;
    }

    /**
     * Accessor: etiqueta legible del usage
     */
    public function getUsageLabelAttribute(): string
    {
        return self::$usageLabels[$this->usage] ?? ucfirst($this->usage);
    }

    /**
     * Accessor: clases CSS del badge de usage
     */
    public function getUsageColorAttribute(): string
    {
        return self::$usageColors[$this->usage] ?? 'bg-gray-100 text-gray-600';
    }
}

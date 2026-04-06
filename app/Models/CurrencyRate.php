<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * 🚀 Master Tip: Incluimos todos los campos de tu migración
     * excepto id y timestamps.
     */
    protected $fillable = [
        'currency_id',
        'buy_rate',
        'sell_rate',
        'official_rate',
        'date',
    ];

    /**
     * Conversión de tipos automática.
     * Esto asegura que los decimales y fechas se comporten correctamente en PHP.
     */
    protected $casts = [
        'buy_rate'      => 'float',
        'sell_rate'     => 'float',
        'official_rate' => 'float',
        'date'          => 'date', // Laravel lo convertirá a un objeto Carbon automáticamente
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Relación: Un tipo de cambio pertenece a una moneda.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

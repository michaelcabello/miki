<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{

    protected $fillable = [
        'name',
        'interval_count',
        'interval_unit',
        'active',
        'order',
    ];

    protected $casts = [
        'active'         => 'boolean',
        'interval_count' => 'integer',
        'order'          => 'integer',
    ];

    // Etiquetas legibles para las unidades de tiempo
    public const INTERVAL_UNITS = [
        'day'   => 'Día',
        'week'  => 'Semana',
        'month' => 'Mes',
        'year'  => 'Año',
    ];

    /**
     * Descripción legible del plan: "Cada 3 meses", "Cada 1 año", etc.
     */
    public function getIntervalLabelAttribute(): string
    {
        $unit  = self::INTERVAL_UNITS[$this->interval_unit] ?? $this->interval_unit;
        $count = $this->interval_count;

        return "Cada {$count} " . ($count === 1 ? $unit : mb_strtolower($unit) . 's');
    }

    /**
     * Plantillas de producto que usan este plan de suscripción.
     * Bloquea la eliminación si el plan está en uso.
     */
    public function productTemplates(): HasMany
    {
        return $this->hasMany(ProductTemplate::class);
    }

    /**
     * Suscripciones activas con este plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Verifica si el plan está en uso (templates o suscripciones).
     */
    public function isInUse(): bool
    {
        return $this->productTemplates()->exists()
            || $this->subscriptions()->exists();
    }
}

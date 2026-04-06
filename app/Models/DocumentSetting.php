<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSetting extends Model
{

    protected $fillable = [
        'comprobante_type_id',
        'template_name',
        'blade_path',
        'paper_size',
        'primary_color',
        'order',
        'activate',
    ];

    public function comprobanteType(): BelongsTo
    {
        return $this->belongsTo(ComprobanteType::class);
    }

    /**
     * Scope para obtener la plantilla que se debe usar actualmente
     */
    public function scopeActive($query)
    {
        return $query->where('activate', true);
    }

    /**
     * Lógica Senior: Al activar una, desactivamos el resto del mismo tipo.
     */
    public function activateTemplate(): void
    {
        self::where('comprobante_type_id', $this->comprobante_type_id)
            ->update(['activate' => false]);

        $this->update(['activate' => true]);
    }
}

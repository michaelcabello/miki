<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
//php artisan make:model Company -m
class Company extends Model
{
    //use HasFactory;
    protected $fillable = [
        'ruc',
        'razonsocial',
        'razonsocialaws',
        'nombrecomercial',
        'direccion',
        'celular',
        'telefono',
        'correo',
        'smtp',
        'password',
        'puerto',
        'department_id',
        'province_id',
        'district_id',
        'ubigeo',
        'logo',
        'soluser',
        'solpass',
        'certificado',
        'certificate_path',
        'fechainiciocertificado',
        'fechafincertificado',
        'cliente_id',
        'cliente_secret',
        'production',
        'state',
        'ublversion',
        'detraccion',
        'pago',
        'currency_id',
    ];

    protected $casts = [
        'production' => 'boolean',
        'state' => 'boolean',
        'fechainiciocertificado' => 'date',
        'fechafincertificado' => 'date',
        'detraccion' => 'decimal:4',
    ];

    protected $appends = [
        'logo_url',
        'full_location',
        'display_name',
    ];


    //relacion de uno a muchos inversa
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    //relacion de uno a muchos inversa
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers principales
    |--------------------------------------------------------------------------
    */

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'state' => true,
                'production' => false,
            ]
        );
    }

    public static function currentOrNull(): ?self
    {
        return static::query()->first();
    }

    public function isProduction(): bool
    {
        return (bool) $this->production;
    }

    public function isActive(): bool
    {
        return (bool) $this->state;
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo);
    }

    public function hasCertificate(): bool
    {
        return !empty($this->certificate_path) || !empty($this->certificado);
    }

    public function hasCompleteUbigeo(): bool
    {
        return !empty($this->department_id)
            && !empty($this->province_id)
            && !empty($this->district_id);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nombrecomercial
            ?: ($this->razonsocial ?: 'Empresa');
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->direccion,
            $this->district?->name,
            $this->province?->name,
            $this->department?->name,
        ]);

        return implode(' - ', $parts);
    }


    public function getLogoUrlAttribute(): string
    {
        if (empty($this->logo)) {
            return asset('img/no-image.png');
        }

        // Si ya es una URL completa
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        // Si es una ruta relativa guardada en S3
        try {
            return Storage::disk('s3_public')->url($this->logo);
        } catch (\Throwable $e) {
            // fallback
        }

        // Si fuera una ruta local dentro de public
        return asset(ltrim($this->logo, '/'));
    }




    public function getLogoUrlAttributeback(): string
    {
        if (empty($this->logo)) {
            return asset('img/no-image.png');
        }


        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        try {
            if (Storage::disk('s3_public')->exists($this->logo)) {
                return Storage::disk('s3_public')->url($this->logo);
            }
        } catch (\Throwable $e) {

        }


        if (file_exists(public_path(ltrim($this->logo, '/')))) {
            return asset(ltrim($this->logo, '/'));
        }

        return asset('img/no-image.png');
    }

    public function getLogoForWatermarkAttribute(): string
    {
        if ($this->hasLogo()) {
            return $this->logo_url;
        }

        return asset('img/marcadeagua.png');
    }

    public function getEnvironmentLabelAttribute(): string
    {
        return $this->isProduction() ? 'Producción' : 'Pruebas';
    }

    public function getEnvironmentBadgeColorAttribute(): string
    {
        return $this->isProduction()
            ? 'bg-green-100 text-green-700'
            : 'bg-yellow-100 text-yellow-700';
    }

    public function getCertificateStatusAttribute(): string
    {
        if (!$this->fechainiciocertificado || !$this->fechafincertificado) {
            return 'Sin fechas';
        }

        $today = now()->startOfDay();

        if ($today->lt($this->fechainiciocertificado)) {
            return 'Pendiente';
        }

        if ($today->gt($this->fechafincertificado)) {
            return 'Vencido';
        }

        return 'Vigente';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{


    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Jerarquía
        |--------------------------------------------------------------------------
        */
        'parent_id',
        'order',

        /*
        |--------------------------------------------------------------------------
        | Identificación
        |--------------------------------------------------------------------------
        */
        'name',
        'company_type_id',
        'document_type_id',
        'document_number',

        /*
        |--------------------------------------------------------------------------
        | Contacto
        |--------------------------------------------------------------------------
        */
        'image',
        'email',
        'phone',
        'whatsapp',
        'mobile',
        'website',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',

        /*
        |--------------------------------------------------------------------------
        | Dirección
        |--------------------------------------------------------------------------
        */
        'street',
        'street2',
        'zip',
        'map',
        'department_id',
        'province_id',
        'district_id',

        /*
        |--------------------------------------------------------------------------
        | Clasificación Comercial
        |--------------------------------------------------------------------------
        */
        'is_customer',
        'is_supplier',
        'customer_rank',
        'supplier_rank',
        'status',

        /*
        |--------------------------------------------------------------------------
        | Comercial / Financiero
        |--------------------------------------------------------------------------
        */
        'pricelist_id',
        'currency_id',
        'bank_account',

        /*
        |--------------------------------------------------------------------------
        | Portal Cliente
        |--------------------------------------------------------------------------
        */
        'portal_access',
        'portal_enabled_at',

        'account_receivable_id',
        'account_payable_id',
    ];




    // Relación con la categoría padre
    public function parent()
    {
        return $this->belongsTo(Partner::class, 'parent_id');
    }

    // Relación con las categorías hijas, para que sea recursiva se pone with('children')
    //lo veremos con laraveldebugbar
    //composer require barryvdh/laravel-debugbar --dev
    public function children()
    {
        return $this->hasMany(Partner::class, 'parent_id')->with(['children', 'parent']); //poner estas 2 para que no exista n+1
    }


    public function companyType()
    {
        return $this->belongsTo(\App\Models\CompanyType::class);
    }

    public function documentType()
    {
        return $this->belongsTo(\App\Models\DocumentType::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }


    public function pricelist()
    {
        return $this->belongsTo(Pricelist::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function department()
    {
        // partner.department_id (string) -> departments.id (string)
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function province()
    {
        // partner.province_id (string) -> provinces.id (string)
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function district()
    {
        // partner.district_id (string) -> districts.id (string)
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    //para que es esto ?
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) return '';
        // Si ya guardas URL completa, retorna tal cual:
        if (str_starts_with($this->image, 'http')) return $this->image;
        // Si es storage local:
        return asset($this->image);
    }

    //relacion con cuentas por cobrar
    public function accountReceivable()
    {
        return $this->belongsTo(Account::class, 'account_receivable_id');
    }
    //relacion con cuentas por pagar
    public function accountPayable()
    {
        return $this->belongsTo(Account::class, 'account_payable_id');
    }
}

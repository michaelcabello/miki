<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'active',
        'type',
        'sale_ok',
        'purchase_ok',
        'pos_ok',
        'active',
        'uom_id',
        'uom_po_id'
    ];

    /* public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    } */

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function purchaseUom()
    {
        return $this->belongsTo(Uom::class, 'uom_po_id');
    }

    public function packagings()
    {
        return $this->hasMany(ProductPackaging::class);
    }



    public function attributeLines()
    {
        return $this->hasMany(\App\Models\ProductTemplateAttribute::class);
    }

    public function variants()
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }

    public function defaultVariant()
    {
        return $this->hasOne(\App\Models\ProductVariant::class)
            ->where('is_default', true);
    }


}

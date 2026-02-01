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
        'active'
        // 'price_base' // si lo usas
    ];

    /* public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    } */


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

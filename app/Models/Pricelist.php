<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    protected $fillable = [
        'name',
        'active',
        'is_default',
        'currency_id',
        'notes'
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function items()
    {
        return $this->hasMany(PricelistItem::class);
    }
}

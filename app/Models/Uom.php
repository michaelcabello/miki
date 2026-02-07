<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    protected $fillable = [
        'uom_category_id',
        'name',
        'symbol',
        'active',
        'uom_type',
        'factor',
        'rounding',
        'sort_order'
    ];

    public function category()
    {
        return $this->belongsTo(UomCategory::class, 'uom_category_id');
    }
}

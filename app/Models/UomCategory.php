<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UomCategory extends Model
{
    protected $fillable = ['name', 'active'];

    public function uoms()
    {
        return $this->hasMany(Uom::class);
    }
}

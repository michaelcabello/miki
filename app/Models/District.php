<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model District -m
class District extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function locals()
    {
        return $this->hasMany(Local::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    //relacion de uno a muchos inversa
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}

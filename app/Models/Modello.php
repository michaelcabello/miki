<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


//php artisan make:model Modello -m
class Modello extends Model
{

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str::slug($name);
    }


    //Relacion uno a muhos inversa
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}

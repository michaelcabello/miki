<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
//php artisan make:model Brand -m
class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'state',
        'order',
        'image',
        'title',
        'description',
        'keywords',
        'created_at',
        'updated_at'
    ];


    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str::slug($name);
    }


    //Relacion uno a muchos
    public function productfamilies()
    {
        return $this->hasMany(Productfamilie::class);
    }

    //Relacion uno a muchos
    public function modellos()
    {
        return $this->hasMany(Modello::class);
    }

    //Relacion muchos a muchos   para las busquedas mas filtradas
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}

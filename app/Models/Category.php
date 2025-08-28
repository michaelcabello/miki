<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
//php artisan make:model Category -m
class Category extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    //URL AMIGABLES
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str::slug($name);
    }

    // Relación con la categoría padre
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relación con las categorías hijas
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    //Relacion muchos a muchos sirve para las busquedas mas filtradas
    public function brands(){
        return $this->belongsToMany(Brand::class);
    }
}

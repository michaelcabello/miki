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

    // Relación con las categorías hijas, para que sea recursiva se pone with('children')
    //lo veremos con laraveldebugbar
    //composer require barryvdh/laravel-debugbar --dev
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with(['children', 'parent']); //poner estas 2 para que no exista n+1
    }

    //Relacion muchos a muchos sirve para las busquedas mas filtradas
    public function brands()
    {
        return $this->belongsToMany(Brand::class);
    }


    // Calcular depth automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (!$category->parent_id) {
                $category->depth = 0;
            } else {
                $category->depth = $category->parent ? $category->parent->depth + 1 : 0;
            }
        });
    }
}

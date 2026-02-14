<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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
    public function modellos()
    {
        return $this->hasMany(Modello::class);
    }

    //Relacion muchos a muchos   para las busquedas mas filtradas
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    protected $casts = [
        'state' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot del modelo
     * Genera automáticamente el slug a partir del nombre
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name')) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        // Eliminar imagen de AWS S3 al eliminar el registro
        static::deleting(function ($brand) {
            if ($brand->image) {
                Storage::disk('s3')->delete($brand->image);
            }
        });
    }

    /**
     * Accessor para obtener la URL completa de la imagen desde AWS S3
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk('s3')->url($this->image);
        }
        return null;
    }


    /**
     * Relación con productos (si existe)
     */
    public function products()
    {
        return $this->hasMany(ProductTemplate::class);
    }

    /**
     * Scope para filtrar solo marcas activas
     */
    public function scopeActive($query)
    {
        return $query->where('state', true);
    }

    /**
     * Scope para ordenar por el campo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}

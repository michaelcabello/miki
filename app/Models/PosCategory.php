<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PosCategory extends Model
{
     protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'state',
        'order',
        'image',
        'title',
        'description',
        'keywords',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (blank($model->slug) && filled($model->name)) {
                $base = Str::slug($model->name);
                $slug = $base;
                $i = 2;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $model->slug = $slug;
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function productTemplates()
    {
        return $this->belongsToMany(
            ProductTemplate::class,
            'pos_category_product_template'
        )->withTimestamps();
    }
}

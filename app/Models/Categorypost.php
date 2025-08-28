<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
//php artisan make:model Categorypost -m
class Categorypost extends Model
{
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str::slug($name);
    }
}

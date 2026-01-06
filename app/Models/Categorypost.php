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

    public function posts()
    {
        return $this->hasmany(Post::class);
    }


    //de uno a muchos
    public function photocategoryposts()
    {
        return $this->hasMany(Photocategorypost::class);
    }

    //Así te aseguras que en $data['state'] siempre tengas true o false, no un string ni null.
    protected $casts = [
        'state' => 'boolean',
    ];
}

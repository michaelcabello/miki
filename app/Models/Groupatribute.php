<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Groupatribute -m
class Groupatribute extends Model
{
    protected $fillable = [
        'name', 'state','order'
    ];

    //de uno a muchos
    public function atributes()
    {
        return $this->hasMany(Atribute::class);
    }
}

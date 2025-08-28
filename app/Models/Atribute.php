<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Atribute -m
class Atribute extends Model
{
    protected $fillable = [
        'name', 'state', 'order'
    ];

    //de uno a muchos inversa
    public function groupatribute()
    {
        return $this->belongsTo(Groupatribute::class);
    }


    //Relacion muchos a muchos
    public function productsatributes()
    {
        return $this->belongsToMany('App\Models\Productatribute');
    }


    //relacion  de uno a muchos
    public function atribute_productatributes()
    {
        return $this->hasMany(Atribute_productatribute::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model Currency -m
class Currency extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    //Relacion uno a muchos
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

}

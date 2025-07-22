<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Department -m
class Department extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    //relacion de uno a muchoa  un departamento tiene uno o muchas provincias
    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function locals()
    {
        return $this->hasMany(Local::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }



}

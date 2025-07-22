<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//php artisan make:model Company -m
class Company extends Model
{
    //use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];


    //relacion de uno a muchos inversa
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    //relacion de uno a muchos inversa
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    //relacion de uno a muchos inversa
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    //relacion de uno a muchos inversa
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}

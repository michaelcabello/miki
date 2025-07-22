<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Employee -m
class Employee extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    const MASCULINO = 1;
    const FEMENINO = 2;

    //Relacion uno a uno
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación de uno a muchos inversa
    public function local()
    {
        return $this->belongsTo(Local::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    // Accessor para el campo 'gender' masculino y femenino
    public function getGenderTextAttribute()
    {
        return $this->gender == self::MASCULINO ? 'Masculino' : 'Femenino';
    }
}

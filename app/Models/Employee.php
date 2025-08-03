<?php

namespace App\Models;
//para el accesor
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Employee -m
class Employee extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    //const MASCULINO = 2;
    //const FEMENINO = 1;


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
    //con este accesor en la vista poner  <td>{{ $user->employee->gender_text }}</td>
   /*  public function getGenderTextAttribute()
    {
        return $this->gender == 1 ? 'Femenino' : 'Masculino';
    } */

    protected function genderText(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->gender == 1 ? 'Femenino' : 'Masculino'
        );
    }
}

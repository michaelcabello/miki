<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Local -m
class Local extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    //relacion de uno a muchos
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    //relacin de uno a muchos inversa  una province pertenece a un departamento
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

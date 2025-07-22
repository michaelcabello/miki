<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model Position -m
class Position extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    //relacion de uno a muchos
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//php artisan make:model Province -m
class Province extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    //relacion deuno a muchos  una provincia tiene uno o muchos distritos

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function districts()
    {
        return $this->hasMany(District::class);
    }
    public function locals()
    {
        return $this->hasMany(Local::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    //relacin de uno a muchos inversa  una province pertenece a un departamento
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

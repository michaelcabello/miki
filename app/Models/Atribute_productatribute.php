<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model Atribute_productatribute
class Atribute_productatribute extends Model
{
    protected $table = "atribute_productatribute";
    protected $guarded = ['id', 'created_at', 'updated_at'];


    //de uno a muchos inversa
    public function productatribute()
    {
        return $this->belongsTo(Productatribute::class);
    }

    //de uno a muchos inversa
    public function atribute()
    {
        return $this->belongsTo(Atribute::class);
    }
}

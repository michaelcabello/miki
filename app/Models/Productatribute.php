<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model Productatribute -m
class Productatribute extends Model
{
    /*     protected $fillable = [
        'codigo', 'price','productfamilie_id','state'//,'name'
    ]; */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    //de uno a muchos inversa
    public function productfamilie()
    {
        return $this->belongsTo(Productfamilie::class);
    }


    //Relacion muchos a muchos//atributes se esta usando en la clase Groupatributes
    //withTimestamps(); para llenar los campos de fechas
    public function atributes()
    {
        return $this->belongsToMany(Atribute::class)->withTimestamps();
    }


    //relacion de muchos a muchos
    public function locales()
    {
        return $this->belongsToMany(Local::class)->withTimestamps()->withPivot('stock', 'stockmin', 'pricesale', 'pricewholesale', 'pricesalemin');
    }


    /* public function initialinventories()
    {
        return $this->belongsToMany(Initialinventory::class)->withTimestamps()->withPivot('stock');
    } */


    //relacion  de uno a muchos
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    //relacion  de uno a muchos
    public function atribute_productatributes()
    {
        return $this->hasMany(Atribute_productatribute::class);
    }

    public function local_productatributes()
    {
        return $this->hasMany(Localproductatribute::class);
    }
}

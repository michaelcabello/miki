<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model StockLot -m
class StockLot extends Model
{
    protected $table = 'stock_lots';

    protected $fillable = [
        'name',
        'productatribute_id',
        'expiration_date',
        'use_date',
        'removal_date',
        'alert_date',
        'reference',
        'note',
        'active',
    ];

    /* ======================
     |  Relaciones
     |======================*/

    // Un lote/serie pertenece a un producto variante (SKU)
    public function productatribute()
    {
        return $this->belongsTo(Productatribute::class);
    }
}

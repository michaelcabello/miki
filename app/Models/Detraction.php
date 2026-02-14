<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detraction extends Model
{
     protected $fillable = [
        'code','name','rate','min_amount',
        'applies_to_sale','applies_to_purchase',
        'active','notes'
    ];
}

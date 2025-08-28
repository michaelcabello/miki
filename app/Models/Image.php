<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model Image -m
class Image extends Model
{
    protected $fillable = [
        'url', 'productatribute_id'

    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//php artisan make:model Season -m
class Season extends Model
{
    protected $fillable = [
        'name',
        'state',
        'order',
        'slug',
        'image',
        'title',
        'description',
        'keywords',
    ];

    protected $casts = [
        'state' => 'boolean',
        'order' => 'integer',
    ];

}

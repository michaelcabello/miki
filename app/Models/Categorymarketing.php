<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorymarketing extends Model
{
     protected $fillable = ['name','order'];

    public function marketings(): HasMany
    {
        return $this->hasMany(Marketing::class);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('order');
    }
}

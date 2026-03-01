<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class JournalType extends Model
{
    protected $fillable = ['code', 'name', 'state', 'order'];

    //cuando lea o guarde estos campos, conviértelos automáticamente a este tipo
    protected $casts = [
        'state' => 'boolean',
        'order' => 'integer',
    ];

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
     protected $fillable = [
        'name','dni','email','phone','movil','birthdate','dateofregistration',
        'message','send','contador','user_id'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'dateofregistration' => 'datetime',
        'send' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function marketings(): BelongsToMany {
        return $this->belongsToMany(Marketing::class)
            ->withPivot('number')->withTimestamps();
    }
}

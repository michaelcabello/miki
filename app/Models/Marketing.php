<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Marketing extends Model
{
    protected $fillable = ['titulo','subject','body','categorymarketing_id','state'];

    protected $casts = ['state' => 'boolean'];

    public function category(): BelongsTo {
        return $this->belongsTo(CategoryMarketing::class, 'categorymarketing_id');
    }

    public function contacts(): BelongsToMany {
        return $this->belongsToMany(Contact::class)->withPivot('number')->withTimestamps();
    }

    // Helper: único activo por categoría
    protected static function booted() {
        static::saving(function (self $marketing) {
            if ($marketing->state) {
                static::where('categorymarketing_id', $marketing->categorymarketing_id)
                    ->whereKeyNot($marketing->id ?? 0)
                    ->update(['state' => false]);
            }
        });
    }
}

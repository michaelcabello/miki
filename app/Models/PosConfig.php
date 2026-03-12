<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosConfig extends Model
{
    protected $fillable = ['name', 'point_of_sales_id', 'journal_id', 'state'];

    public function pointOfSale() {
        return $this->belongsTo(PointOfSale::class, 'point_of_sales_id');
    }

    public function journal() {
        return $this->belongsTo(Journal::class);
    }

    public function sessions() {
        return $this->hasMany(PosSession::class);
    }
}

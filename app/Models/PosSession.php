<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSession extends Model
{
    protected $fillable = ['pos_config_id', 'user_id', 'start_at', 'stop_at', 'state', 'balance_start', 'balance_end_real'];

    public function config() {
        return $this->belongsTo(PosConfig::class, 'pos_config_id');
    }

    public function payments() {
        return $this->hasMany(PosPayment::class);
    }

    public function cashDetails() {
        return $this->hasMany(PosSessionCashDetail::class);
    }
}

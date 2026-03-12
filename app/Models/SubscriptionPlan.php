<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'interval_count', 'interval_unit', 'active'];

    public function producttemplates()
    {
        return $this->hasMany(ProductTemplate::class);
    }
}

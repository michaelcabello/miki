<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'partner_id',
        'product_id',
        'subscription_plan_id',
        'recurring_price',
        'start_date',
        'next_billing_date',
        'status',
        'last_move_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_billing_date' => 'date',
    ];

    // Relaciones
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    public function product()
    {
        return $this->belongsTo(ProductTemplate::class);
    }
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Scopes para el Cron Job
    public function scopeToInvoice($query)
    {
        return $query->where('status', 'active')
            ->where('next_billing_date', '<=', now());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'amount_type',
        'type_tax_use',
        'tax_scope',
        'sequence',
        'company_id',
        'tax_group_id',
        'cash_basis_transition_account_id',
        'price_include',
        'include_base_amount',
        'is_base_affected',
        'active',
        'description',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}

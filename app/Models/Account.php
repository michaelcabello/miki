<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    protected $fillable = [
        'parent_id',
        'code',
        'equivalent_code',
        'name',
        'account_type_id',
        'reconcile',
        'cost_center',
        'current_account',
        'depth',
        'path',
    ];

    // Relación recursiva
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

   /*  public function children()
    {
        return $this->hasMany(Account::class, 'parent_id')->with(['children', 'parent']);
    } */


    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id')
            ->with(['children', 'accountType'])
            ->orderByRaw('LPAD(code, 20, "0")');
    }




    /* public function accountType() //
    {
        return $this->belongsTo(AccountType::class);
    } */

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }
}

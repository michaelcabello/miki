<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//php artisan make:model AccountType -m
//estamos creando el modelo AccountType y tabla account_types (plural)
//la tabla estara en plural
//cuando son relaciones de muchos a muchos la tabla generada tiene guion bajo y es en  singular
class AccountType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'order',
        'sub_account_type_id',
    ];

    // 🔄 Relación con sub tipo
    public function subAccountType(): BelongsTo
    {
        return $this->belongsTo(SubAccountType::class, 'sub_account_type_id');
    }


    public function accounts()
    {
        return $this->hasMany(Account::class,);
    }
}

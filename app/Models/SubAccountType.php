<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//php artisan make:model SubAccountType -m
//estamos creando el modelo SubAccountType y tabla sub_account_types
//la tabla estara en plural
//cuando son relaciones de muchos a muchos la tabla generada tiene guion bajo y es en  singular
class SubAccountType extends Model
{
   protected $fillable = [
        'name',
        'order',
    ];

    // 📌 Tipos de cuentas que pertenecen a este sub-tipo
    // hasmany es la forma mas moderna de declarar no olvidar imporyar el suse arribas
    public function accountTypes(): HasMany
    {
        return $this->hasMany(AccountType::class, 'sub_account_type_id');
    }
}

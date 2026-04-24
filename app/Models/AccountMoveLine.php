<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountMoveLine extends Model
{
    // 🚀 IMPORTANTE: Todos estos campos deben estar aquí para evitar el error de la imagen
    protected $fillable = [
        'account_move_id',
        'account_id',
        'product_variant_id',
        'partner_id',
        'name',
        'quantity',
        'price_unit',    // 👈 Este es el que faltaba
        'discount',
        'debit',
        'credit',
        'amount_currency',
        'currency_id',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:4',
            'price_unit' => 'decimal:4',
            'debit'      => 'decimal:4',
            'credit'     => 'decimal:4',
        ];
    }

    /* --- Relaciones --- */

    public function move(): BelongsTo
    {
        return $this->belongsTo(AccountMove::class, 'account_move_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

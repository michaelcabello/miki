<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountSetting extends Model
{
    protected $table = 'account_settings';

    protected $fillable = [
        // Cuentas por cobrar / pagar
    'default_receivable_account_id',
    'default_payable_account_id',

    // Fallback generales
    'default_income_account_id',
    'default_expense_account_id',

    // 🔥 NUEVO: Ingresos por tipo
    'default_income_goods_account_id',
    'default_income_service_account_id',

    // 🔥 NUEVO: Gastos por tipo
    'default_expense_goods_account_id',
    'default_expense_service_account_id',

    // Impuestos
    'default_sales_tax_account_id',
    'default_purchase_tax_account_id',

    // Otros
    'rounding_account_id',
    'active',
    'settings',
    ];

    protected $casts = [
        'active' => 'boolean',
        'settings' => 'array',
    ];
}

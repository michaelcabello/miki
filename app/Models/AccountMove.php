<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountMove extends Model
{
    // 🚀 Campos habilitados para asignación masiva
    protected $fillable = [
        'name',              // Secuencia (BILL/2026/0001)
        'move_type',         // Tipo: in_invoice, out_invoice, entry
        'state',             // draft, posted, cancel
        'partner_id',        // Proveedor o Cliente
        'journal_id',        // Diario contable (Compras, Ventas, Caja)
        'currency_id',       // Moneda
        'purchase_order_id', // Trazabilidad con la Orden de Compra
        'amount_untaxed',    // Subtotal
        'amount_tax',        // Impuestos
        'amount_total',      // Total facturado
        'currency_rate',     // Tipo de cambio aplicado
        'date',              // Fecha contable
        'invoice_date',      // Fecha de la factura física
        'notes',             // Comentarios internos
    ];

    /**
     * 🕒 Casts de Laravel 12 para precisión contable
     */
    protected function casts(): array
    {
        return [
            'date'           => 'date',
            'invoice_date'   => 'date',
            'amount_untaxed' => 'decimal:4',
            'amount_tax'     => 'decimal:4',
            'amount_total'   => 'decimal:4',
            'currency_rate'  => 'decimal:6',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones (Arquitectura Odoo)
    |--------------------------------------------------------------------------
    */

    /**
     * Líneas del asiento contable (Apuntes)
     */
    public function lines(): HasMany
    {
        return $this->hasMany(AccountMoveLine::class, 'account_move_id');
    }

    /**
     * Vínculo con la Orden de Compra (Smart Button)
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Socio (Proveedor/Cliente)
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}

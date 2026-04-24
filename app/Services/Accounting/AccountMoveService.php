<?php

namespace App\Services\Accounting;

use App\Models\{AccountMove, AccountMoveLine, PurchaseOrder};
use Illuminate\Support\Facades\DB;

class AccountMoveService
{
    public function createFromPurchaseOrder(PurchaseOrder $order): AccountMove
    {
        return DB::transaction(function () use ($order) {
            // 1. Crear la Cabecera de la Factura (Vendor Bill)
            $move = AccountMove::create([
                'name' => 'BILL/' . now()->format('Y/m/') . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'move_type' => 'in_invoice',
                'state' => 'draft',
                'partner_id' => $order->partner_id,
                'journal_id' => 2, // Diario de Compras
                'currency_id' => $order->currency_id,
                'purchase_order_id' => $order->id,
                'amount_untaxed' => $order->amount_untaxed,
                'amount_tax' => $order->amount_tax,
                'amount_total' => $order->amount_total,
                'date' => now(),
            ]);

            // 2. Crear las Líneas de Asiento (Partida Doble)
            foreach ($order->lines as $line) {
                // A) LÍNEA DE GASTO (DEBE)
                AccountMoveLine::create([
                    'account_move_id'    => $move->id,
                    'account_id'         => $line->product->template->property_account_expense_id ?? 1,
                    'product_variant_id' => $line->product_id,
                    'partner_id'         => $order->partner_id,
                    'name'               => $line->name,
                    'quantity'           => $line->product_qty,
                    'price_unit'         => $line->price_unit, // 🚀 REQUERIDO
                    'debit'              => $line->price_subtotal,
                    'credit'             => 0,
                    'currency_id'        => $order->currency_id,
                ]);
            }

            // B) LÍNEA DE PASIVO - CXP (HABER)
            // Esta es la línea que estaba causando el error en la imagen
            AccountMoveLine::create([
                'account_move_id'    => $move->id,
                'account_id'         => $order->partner->account_payable_id ?? 2,
                'partner_id'         => $order->partner_id,
                'product_variant_id' => null, // El proveedor no es un producto
                'name'               => "Pago pendiente: {$order->name}",
                'quantity'           => 1,
                'price_unit'         => $order->amount_total, // 🚀 REQUERIDO: Aquí estaba el fallo
                'debit'              => 0,
                'credit'             => $order->amount_total,
                'currency_id'        => $order->currency_id,
            ]);

            return $move;
        });
    }
}

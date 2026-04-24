<?php

namespace App\Services\Stock;

use App\Models\StockPicking;
use App\Models\StockMove;
use App\Models\StockQuant;
use App\Models\StockLot;
use Illuminate\Support\Facades\DB;

class StockService
{

    // app/Services/Stock/StockService.php

    public function validateReception(StockPicking $picking, array $linesData)
    {

        // 🛡️ REGLA DE ORO: Si ya está hecho, abortamos
        if ($picking->state === 'done') {
            throw new \Exception("Esta recepción ya fue validada anteriormente.");
        }

        return DB::transaction(function () use ($picking, $linesData) {
            foreach ($linesData as $data) {
                $move = StockMove::findOrFail($data['id']);

                // 1. Actualizamos el movimiento a 'hecho'
                $move->update([
                    'qty_done' => $data['qty_done'],
                    'state'    => 'done',
                ]);

                // 2. 📦 MOVIMIENTO DE STOCK FÍSICO (Solución al TypeError)
                // Primero: Aseguramos que el registro existe (si no, lo crea con cantidad 0)
                $quant = StockQuant::firstOrCreate(
                    [
                        'product_variant_id' => $move->product_variant_id,
                        'location_id'        => $move->location_to_id,
                        'lot_id'             => null, // Opción A: Sin lotes por ahora
                    ],
                    [
                        'quantity' => 0 // Valor inicial si es nuevo
                    ]
                );

                // Segundo: Usamos el método increment().
                // Esto ejecuta "UPDATE quantity = quantity + X" directamente en SQL
                // evitando el conflicto con el Cast de Eloquent.
                //$quant->increment('quantity', $data['qty_done']);
                $quant->increment('quantity', (float) $data['qty_done']);
            }

            // 3. Finalizamos el Picking
            $picking->update([
                'state'     => 'done',
                'date_done' => now()
            ]);

            return $picking;
        });
    }





    protected function updatePhysicalInventory(StockMove $move)
    {
        // En Odoo, aquí se crearía un 'Stock Quant'
        // Por ahora, sumaremos directamente a la variante para tu erp2027
        $variant = $move->productVariant;
        $variant->increment('stock_actual', $move->qty_done);
    }
}

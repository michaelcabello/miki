<?php

namespace App\Observers;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseObserver
{
    /**
     * Se ejecuta SOLO al crear un almacén nuevo.
     * Crea las ubicaciones estándar y vincula lot_stock_id.
     */
    public function created(Warehouse $warehouse): void
    {
        // Protección: si ya tiene lot_stock_id no volver a crear
        if ($warehouse->lot_stock_id) {
            return;
        }

        DB::transaction(function () use ($warehouse) {
            // 1. Ubicación raíz (view) — agrupa todo el árbol del almacén
            $root = WarehouseLocation::create([
                'code'          => $warehouse->code,
                'name'          => $warehouse->name,
                'complete_name' => $warehouse->code,
                'warehouse_id'  => $warehouse->id,
                'parent_id'     => null,
                'usage'         => 'view',
                'order'         => 0,
                'state'         => true,
            ]);

            // 2. Ubicación principal de stock (internal)
            // Esta es la que lot_stock_id va a referenciar
            $stock = WarehouseLocation::create([
                'code'          => 'STOCK',
                'name'          => 'Stock',
                'complete_name' => $warehouse->code . ' / Stock',
                'warehouse_id'  => $warehouse->id,
                'parent_id'     => $root->id,
                'usage'         => 'internal',
                'order'         => 1,
                'state'         => true,
            ]);

            // 3. Ubicaciones estándar adicionales
            $extras = [
                ['code' => 'INPUT',  'name' => 'Input',  'usage' => 'internal',  'order' => 2, 'scrap' => false],
                ['code' => 'OUTPUT', 'name' => 'Output', 'usage' => 'internal',  'order' => 3, 'scrap' => false],
                ['code' => 'SCRAP',  'name' => 'Scrap',  'usage' => 'inventory', 'order' => 4, 'scrap' => true],
            ];

            foreach ($extras as $extra) {
                WarehouseLocation::create([
                    'code'           => $extra['code'],
                    'name'           => $extra['name'],
                    'complete_name'  => $warehouse->code . ' / ' . $extra['name'],
                    'warehouse_id'   => $warehouse->id,
                    'parent_id'      => $root->id,
                    'usage'          => $extra['usage'],
                    'scrap_location' => $extra['scrap'],
                    'order'          => $extra['order'],
                    'state'          => true,
                ]);
            }

            // 4. Vincular lot_stock_id — usar updateQuietly para no
            // disparar de nuevo el observer ni eventos innecesarios
            $warehouse->updateQuietly(['lot_stock_id' => $stock->id]);
        });
    }
}

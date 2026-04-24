<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\StockOperationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::transaction(function () {

            // --- 1. UBICACIONES VIRTUALES GLOBALES ---
            $partnersView = WarehouseLocation::updateOrCreate(
                ['code' => 'PARTNERS', 'warehouse_id' => null],
                [
                    'name' => 'Partners',
                    'complete_name' => 'Partners',
                    'usage' => 'view',
                ]
            );

            $vendorsLoc = WarehouseLocation::updateOrCreate(
                ['code' => 'VENDORS', 'warehouse_id' => null],
                [
                    'name' => 'Vendors',
                    'complete_name' => 'Partners/Vendors',
                    'parent_id' => $partnersView->id,
                    'usage' => 'supplier',
                ]
            );

            $customersLoc = WarehouseLocation::updateOrCreate(
                ['code' => 'CUSTOMERS', 'warehouse_id' => null],
                [
                    'name' => 'Customers',
                    'complete_name' => 'Partners/Customers',
                    'parent_id' => $partnersView->id,
                    'usage' => 'customer',
                ]
            );

            // --- 2. ALMACÉN PRINCIPAL ---
            $whCentral = Warehouse::updateOrCreate(
                ['code' => 'WH'],
                [
                    'name' => 'Almacén Central',
                    'address' => 'Av. Principal 123, Lima',
                    'is_main' => true,
                    'state' => true,
                ]
            );

            // --- 3. JERARQUÍA DE UBICACIONES DEL ALMACÉN ---
            $whView = WarehouseLocation::updateOrCreate(
                ['code' => 'WH', 'warehouse_id' => $whCentral->id],
                [
                    'name' => 'WH',
                    'complete_name' => 'WH',
                    'usage' => 'view',
                ]
            );

            $whStock = WarehouseLocation::updateOrCreate(
                ['code' => 'STOCK', 'warehouse_id' => $whCentral->id],
                [
                    'name' => 'Stock',
                    'complete_name' => 'WH/Stock',
                    'parent_id' => $whView->id,
                    'usage' => 'internal',
                ]
            );

            $whCentral->update(['lot_stock_id' => $whStock->id]);

            WarehouseLocation::updateOrCreate(
                ['code' => 'SCRAP', 'warehouse_id' => $whCentral->id],
                [
                    'name' => 'Scrap',
                    'complete_name' => 'WH/Scrap',
                    'parent_id' => $whView->id,
                    'usage' => 'inventory',
                    'scrap_location' => true,
                ]
            );

            // --- 4. TIPOS DE OPERACIÓN ---
            $ops = [
                [
                    'name' => 'Recepciones',
                    'type' => 'incoming',
                    'prefix' => 'IN',
                    'src' => $vendorsLoc->id,
                    'dest' => $whStock->id
                ],
                [
                    'name' => 'Órdenes de Entrega',
                    'type' => 'outgoing',
                    'prefix' => 'OUT',
                    'src' => $whStock->id,
                    'dest' => $customersLoc->id
                ],
                [
                    'name' => 'Transferencias Internas',
                    'type' => 'internal',
                    'prefix' => 'INT',
                    'src' => $whStock->id,
                    'dest' => $whStock->id
                ],
            ];

            foreach ($ops as $op) {
                StockOperationType::updateOrCreate(
                    ['name' => $op['name'], 'warehouse_id' => $whCentral->id],
                    [
                        'type' => $op['type'],
                        'sequence_prefix' => $op['prefix'],
                        'sequence_number' => 1,
                        'default_location_src_id' => $op['src'],
                        'default_location_dest_id' => $op['dest'],
                    ]
                );
            }

            // --- 5. TIENDA SECUNDARIA ---
            $shop = Warehouse::updateOrCreate(
                ['code' => 'TIENDA1'],
                [
                    'name' => 'Tienda Miraflores',
                    'address' => 'Calle Alcanfores 456',
                    'is_main' => false,
                    'state' => true,
                ]
            );

            $shopView = WarehouseLocation::updateOrCreate(
                ['code' => 'T1', 'warehouse_id' => $shop->id],
                [
                    'name' => 'T1',
                    'complete_name' => 'T1',
                    'usage' => 'view',
                ]
            );

            $shopStock = WarehouseLocation::updateOrCreate(
                ['code' => 'STOCK', 'warehouse_id' => $shop->id],
                [
                    'name' => 'Stock',
                    'complete_name' => 'T1/Stock',
                    'parent_id' => $shopView->id,
                    'usage' => 'internal',
                ]
            );

            $shop->update(['lot_stock_id' => $shopStock->id]);

            StockOperationType::updateOrCreate(
                ['name' => 'Punto de Venta', 'warehouse_id' => $shop->id],
                [
                    'type' => 'outgoing',
                    'sequence_prefix' => 'POS',
                    'sequence_number' => 1,
                    'default_location_src_id' => $shopStock->id,
                    'default_location_dest_id' => $customersLoc->id,
                ]
            );
        });
    }
}

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

            // --- 1. UBICACIONES VIRTUALES GLOBALES (Fuera de los almacenes) ---
            // Odoo necesita saber de dónde viene la mercadería (Proveedores) y a dónde va (Clientes)

            $partnersView = WarehouseLocation::create([
                'code' => 'PARTNERS',
                'name' => 'Partners',
                'complete_name' => 'Partners',
                'usage' => 'view',
            ]);

            $vendorsLoc = WarehouseLocation::create([
                'code' => 'VENDORS',
                'name' => 'Vendors',
                'complete_name' => 'Partners/Vendors',
                'parent_id' => $partnersView->id,
                'usage' => 'supplier',
            ]);

            $customersLoc = WarehouseLocation::create([
                'code' => 'CUSTOMERS',
                'name' => 'Customers',
                'complete_name' => 'Partners/Customers',
                'parent_id' => $partnersView->id,
                'usage' => 'customer',
            ]);

            // --- 2. CREACIÓN DEL ALMACÉN PRINCIPAL (Retail Central) ---

            $whCentral = Warehouse::create([
                'code' => 'WH',
                'name' => 'Almacén Central',
                'address' => 'Av. Principal 123, Lima',
                'is_main' => true,
                'is_active' => true,
            ]);

            // --- 3. JERARQUÍA DE UBICACIONES DEL ALMACÉN ---

            // Ubicación Raíz del Almacén (View)
            $whView = WarehouseLocation::create([
                'code' => 'WH',
                'name' => 'WH',
                'complete_name' => 'WH',
                'usage' => 'view',
                'warehouse_id' => $whCentral->id,
            ]);

            // Ubicación de Stock Principal (Internal) - Aquí vive el inventario
            $whStock = WarehouseLocation::create([
                'code' => 'STOCK',
                'name' => 'Stock',
                'complete_name' => 'WH/Stock',
                'parent_id' => $whView->id,
                'usage' => 'internal',
                'warehouse_id' => $whCentral->id,
            ]);

            // Actualizamos el Almacén con su ubicación de stock principal (Odoo Style)
            $whCentral->update(['lot_stock_id' => $whStock->id]);

            // Ubicación de Merma (Scrap)
            WarehouseLocation::create([
                'code' => 'SCRAP',
                'name' => 'Scrap',
                'complete_name' => 'WH/Scrap',
                'parent_id' => $whView->id,
                'usage' => 'inventory',
                'scrap_location' => true,
                'warehouse_id' => $whCentral->id,
            ]);

            // --- 4. TIPOS DE OPERACIÓN (Secuencias y Flujos) ---

            // RECEPCIONES (Ingresos de Proveedores)
            StockOperationType::create([
                'name' => 'Recepciones',
                'type' => 'incoming',
                'sequence_prefix' => 'IN',
                'sequence_number' => 1,
                'warehouse_id' => $whCentral->id,
                'default_location_src_id' => $vendorsLoc->id,
                'default_location_dest_id' => $whStock->id,
            ]);

            // ÓRDENES DE ENTREGA (Salidas a Clientes)
            StockOperationType::create([
                'name' => 'Órdenes de Entrega',
                'type' => 'outgoing',
                'sequence_prefix' => 'OUT',
                'sequence_number' => 1,
                'warehouse_id' => $whCentral->id,
                'default_location_src_id' => $whStock->id,
                'default_location_dest_id' => $customersLoc->id,
            ]);

            // TRANSFERENCIAS INTERNAS
            StockOperationType::create([
                'name' => 'Transferencias Internas',
                'type' => 'internal',
                'sequence_prefix' => 'INT',
                'sequence_number' => 1,
                'warehouse_id' => $whCentral->id,
                'default_location_src_id' => $whStock->id,
                'default_location_dest_id' => $whStock->id,
            ]);

            // --- 5. CREACIÓN DE UNA TIENDA SECUNDARIA (Retail Shop) ---

            $shop = Warehouse::create([
                'code' => 'TIENDA1',
                'name' => 'Tienda Miraflores',
                'address' => 'Calle Alcanfores 456',
                'is_main' => false,
                'is_active' => true,
            ]);

            $shopView = WarehouseLocation::create([
                'code' => 'T1',
                'name' => 'T1',
                'complete_name' => 'T1',
                'usage' => 'view',
                'warehouse_id' => $shop->id,
            ]);

            $shopStock = WarehouseLocation::create([
                'code' => 'STOCK',
                'name' => 'Stock',
                'complete_name' => 'T1/Stock',
                'parent_id' => $shopView->id,
                'usage' => 'internal',
                'warehouse_id' => $shop->id,
            ]);

            $shop->update(['lot_stock_id' => $shopStock->id]);

            // Tipo de Operación para la Tienda (Punto de Venta)
            StockOperationType::create([
                'name' => 'Punto de Venta',
                'type' => 'outgoing',
                'sequence_prefix' => 'POS',
                'sequence_number' => 1,
                'warehouse_id' => $shop->id,
                'default_location_src_id' => $shopStock->id,
                'default_location_dest_id' => $customersLoc->id,
            ]);
        });
    }
}

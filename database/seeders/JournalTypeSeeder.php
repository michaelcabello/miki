<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\JournalType;

class JournalTypeSeeder extends Seeder
{
    /**
     * Carga los tipos de diario base del ERP.
     * Es idempotente: se puede correr múltiples veces sin duplicar registros.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // ── Core Odoo-like ──────────────────────────────────────────
            ['code' => 'SALE',  'name' => 'Ventas',                       'state' => true, 'order' => 10],
            ['code' => 'PURCH', 'name' => 'Compras',                      'state' => true, 'order' => 20],
            ['code' => 'BANK',  'name' => 'Banco',                        'state' => true, 'order' => 30],
            ['code' => 'CASH',  'name' => 'Caja',                         'state' => true, 'order' => 40],
            ['code' => 'MISC',  'name' => 'Misceláneo',                   'state' => true, 'order' => 50],

            // ── Comunes en ERP retail ────────────────────────────────────
            ['code' => 'POS',   'name' => 'Punto de Venta (POS)',         'state' => true, 'order' => 60],
            ['code' => 'INV',   'name' => 'Inventario / Valorización',    'state' => true, 'order' => 70],
            ['code' => 'PAY',   'name' => 'Sueldos / Planilla',           'state' => true, 'order' => 80],
            ['code' => 'ASSET', 'name' => 'Activos Fijos / Depreciación', 'state' => true, 'order' => 90],
            ['code' => 'FX',    'name' => 'Diferencia de Cambio',         'state' => true, 'order' => 100],
            ['code' => 'MFG',   'name' => 'Producción / Manufactura',     'state' => true, 'order' => 110],
            ['code' => 'ADV',   'name' => 'Anticipos',                    'state' => true, 'order' => 120],
            ['code' => 'RECON', 'name' => 'Conciliación',                 'state' => true, 'order' => 130],
        ];

        foreach ($rows as $row) {
            JournalType::updateOrCreate(
                // Clave de búsqueda — no duplica si ya existe
                ['code' => $row['code']],
                // Datos a insertar o actualizar
                [
                    'name'       => $row['name'],
                    'state'      => $row['state'],
                    'order'      => $row['order'],
                    'created_at' => $now, // solo aplica en INSERT, Eloquent lo respeta
                    'updated_at' => $now,
                ]
            );
        }

        $this->command->info('✅ JournalTypeSeeder ejecutado: ' . count($rows) . ' tipos de diario cargados.');
    }
}

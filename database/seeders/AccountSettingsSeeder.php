<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\Account;


class AccountSettingsSeeder extends Seeder
{


    public function run(): void
    {
        /**
         * account_settings es 1 fila (config global).
         * Buscamos cuentas por CODE para no depender de IDs.
         */

        $codes = [
            // Ventas
            'default_receivable_account_id'   => '1212',   // 12 - CxC (Emitidas en cartera)
            'default_income_account_id'       => '70111',  // 70 - Ventas mercaderías terceros
            'default_sales_tax_account_id'    => '40111',  // 40 - IGV (cuenta propia) (venta)

            // Compras
            'default_payable_account_id'      => '421',    // 42 - CxP (Facturas por pagar)
            'default_expense_account_id'      => '601',    // 60 - Compras mercaderías (ajústalo si quieres)
            'default_purchase_tax_account_id' => '40111',  // temporal (luego separas crédito fiscal si deseas)

            // Opcional
            'rounding_account_id'             => null,     // si aún no tienes cuenta de redondeo
        ];

        // Resolver IDs por code
        $ids = [];

        foreach ($codes as $column => $code) {
            if ($code === null) {
                $ids[$column] = null;
                continue;
            }

            $acc = Account::query()->where('code', $code)->first();

            if (! $acc) {
                throw new \RuntimeException("No existe la cuenta con code={$code} para llenar {$column}. Ejecuta primero AccountSeederdos.");
            }

            $ids[$column] = $acc->id;
        }

        // Insertar o actualizar la única fila (id=1)
        DB::table('account_settings')->updateOrInsert(
            ['id' => 1],
            [
                'default_receivable_account_id'   => $ids['default_receivable_account_id'],
                'default_payable_account_id'      => $ids['default_payable_account_id'],
                'default_income_account_id'       => $ids['default_income_account_id'],
                'default_expense_account_id'      => $ids['default_expense_account_id'],
                'default_sales_tax_account_id'    => $ids['default_sales_tax_account_id'],
                'default_purchase_tax_account_id' => $ids['default_purchase_tax_account_id'],
                'rounding_account_id'             => $ids['rounding_account_id'],

                'active'   => true,
                'settings' => json_encode([
                    'note' => 'Config contable por defecto (fallback).',
                    'version' => 1,
                ]),

                'updated_at' => now(),
                // para que no falle si es insert
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );
    }
}

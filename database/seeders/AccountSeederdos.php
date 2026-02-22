<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\AccountType;

class AccountSeederdos extends Seeder
{
    private array $typeIdsByOrder = [];   // order => id
    private array $rootAccountsByCode = []; // '0'..'9' => Account

    public function run(): void
    {
        // 1) Mapear AccountTypes por "order" (NO por ID fijo)
        $this->typeIdsByOrder = AccountType::query()
            ->get(['id', 'order'])
            ->keyBy('order')
            ->map(fn ($x) => (int) $x->id)
            ->all();

        // Validación mínima (por si no corriste AccountTypeSeeder)
        foreach ([1,2,3,4,5,6,7,8] as $ord) {
            if (!isset($this->typeIdsByOrder[$ord])) {
                throw new \RuntimeException("Falta AccountType con order={$ord}. Ejecuta primero AccountTypeSeeder.");
            }
        }

        // 2) Crear raíces 0–9 (clases PCGE)
        $roots = [
            ['code' => '0', 'name' => 'CUENTAS DE ORDEN', 'type_order' => 8],
            ['code' => '1', 'name' => 'ACTIVO DISPONIBLE Y EXIGIBLE', 'type_order' => 1],
            ['code' => '2', 'name' => 'ACTIVO REALIZABLE', 'type_order' => 1],
            ['code' => '3', 'name' => 'ACTIVO INMOVILIZADO', 'type_order' => 1],
            ['code' => '4', 'name' => 'PASIVO', 'type_order' => 2],
            ['code' => '5', 'name' => 'PATRIMONIO NETO', 'type_order' => 3],
            ['code' => '6', 'name' => 'GASTOS POR NATURALEZA', 'type_order' => 4],
            ['code' => '7', 'name' => 'INGRESOS', 'type_order' => 5],
            ['code' => '8', 'name' => 'SALDOS INTERMEDIARIOS DE GESTIÓN Y DETERMINACIÓN DEL RESULTADO DEL EJERCICIO', 'type_order' => 6],
            ['code' => '9', 'name' => 'CONTABILIDAD ANALÍTICA DE EXPLOTACIÓN: COSTOS DE PRODUCCIÓN Y GASTOS POR FUNCIÓN', 'type_order' => 7],
        ];

        foreach ($roots as $r) {
            $acc = Account::updateOrCreate(
                ['code' => $r['code']],
                [
                    'name' => $r['name'],
                    'parent_id' => null,
                    'account_type_id' => $this->typeIdsByOrder[$r['type_order']],
                    'reconcile' => false,
                    'costcenter' => false,
                    'isrecord' => false,
                    'depth' => 0,
                    'path' => $r['code'],
                    'tag' => null,
                    'equivalentcode' => null,
                    'tax_id' => null,
                ]
            );

            $this->rootAccountsByCode[$r['code']] = $acc;
        }

        // 3) Árbol de cuentas para compras/ventas/inventario/IGV (colgar bajo raíz)

$tree = [
    // 10 Caja y bancos -> Activo (raíz 1)
    ['code' => '10', 'name' => 'CAJA Y BANCOS', 'root' => '1', 'children' => [
        ['code' => '101', 'name' => 'Caja', 'children' => [
            ['code' => '1011', 'name' => 'Caja principal', 'isrecord' => true],
        ]],
        ['code' => '104', 'name' => 'Cuentas corrientes en instituciones financieras', 'children' => [
            ['code' => '1041', 'name' => 'Cuentas corrientes operativas', 'children' => [
                ['code' => '104101', 'name' => 'BCP - Soles', 'isrecord' => true, 'reconcile' => true],
                ['code' => '104102', 'name' => 'BCP - Dólares', 'isrecord' => true, 'reconcile' => true],
                ['code' => '104201', 'name' => 'BBVA - Soles', 'isrecord' => true, 'reconcile' => true],
            ]],
            ['code' => '1042', 'name' => 'Cuentas corrientes para fines específicos', 'isrecord' => true, 'reconcile' => true],
        ]],
    ]],

    // 12 CxC -> Activo (raíz 1)
    ['code' => '12', 'name' => 'CUENTAS POR COBRAR COMERCIALES – TERCEROS', 'root' => '1', 'children' => [
        ['code' => '121', 'name' => 'Facturas, boletas y otros comprobantes por cobrar', 'children' => [
            ['code' => '1212', 'name' => 'Emitidas en cartera', 'isrecord' => true, 'reconcile' => true],
            ['code' => '1213', 'name' => 'En cobranza', 'isrecord' => true, 'reconcile' => true],
        ]],
        ['code' => '122', 'name' => 'Anticipos recibidos de clientes', 'isrecord' => true, 'reconcile' => true],
    ]],

    // 20 Mercaderías -> Activo realizable (raíz 2)
    ['code' => '20', 'name' => 'MERCADERÍAS', 'root' => '2', 'children' => [
        ['code' => '201', 'name' => 'Mercaderías', 'children' => [
            ['code' => '2011', 'name' => 'Mercaderías', 'children' => [
                ['code' => '20111', 'name' => 'Costo', 'isrecord' => true],
                ['code' => '20112', 'name' => 'Valor razonable', 'isrecord' => true],
            ]],
        ]],
        ['code' => '209', 'name' => 'Mercaderías desvalorizadas', 'isrecord' => true],
    ]],

    // ✅ 33 Propiedad, planta y equipo -> Activo inmovilizado (raíz 3)
    ['code' => '33', 'name' => 'PROPIEDAD, PLANTA Y EQUIPO', 'root' => '3', 'children' => [
        ['code' => '331', 'name' => 'Terrenos', 'isrecord' => true],
        ['code' => '332', 'name' => 'Edificaciones', 'isrecord' => true],
        ['code' => '333', 'name' => 'Maquinarias y equipos de explotación', 'isrecord' => true],
        ['code' => '334', 'name' => 'Unidades de transporte', 'isrecord' => true],
        ['code' => '335', 'name' => 'Muebles y enseres', 'isrecord' => true],
        ['code' => '336', 'name' => 'Equipos diversos', 'isrecord' => true],
        ['code' => '337', 'name' => 'Herramientas', 'isrecord' => true],
    ]],

    // ✅ 39 Depreciación y amortización acumuladas -> Activo inmovilizado (raíz 3)
    ['code' => '39', 'name' => 'DEPRECIACIÓN, AMORTIZACIÓN Y AGOTAMIENTO ACUMULADOS', 'root' => '3', 'children' => [
        ['code' => '391', 'name' => 'Depreciación acumulada', 'children' => [
            ['code' => '3911', 'name' => 'Depreciación acumulada - PPE', 'isrecord' => true],
        ]],
        ['code' => '392', 'name' => 'Amortización acumulada', 'children' => [
            ['code' => '3921', 'name' => 'Amortización acumulada - Intangibles', 'isrecord' => true],
        ]],
    ]],

    // 40 Tributos (IGV por pagar) -> Pasivo (raíz 4)
    ['code' => '40', 'name' => 'TRIBUTOS Y APORTES POR PAGAR', 'root' => '4', 'children' => [
        ['code' => '401', 'name' => 'Gobierno central', 'children' => [
            ['code' => '4011', 'name' => 'Impuesto general a las ventas', 'children' => [
                ['code' => '40111', 'name' => 'IGV - Cuenta propia', 'isrecord' => true],
                ['code' => '40114', 'name' => 'IGV - Retenciones', 'isrecord' => true],
                ['code' => '40113', 'name' => 'IGV - Percepciones', 'isrecord' => true],
            ]],
        ]],
    ]],

    // 42 CxP -> Pasivo (raíz 4)
    ['code' => '42', 'name' => 'CUENTAS POR PAGAR COMERCIALES - TERCEROS', 'root' => '4', 'children' => [
        ['code' => '421', 'name' => 'Facturas, boletas y otros comprobantes por pagar', 'isrecord' => true, 'reconcile' => true],
        ['code' => '422', 'name' => 'Anticipos a proveedores', 'isrecord' => true, 'reconcile' => true],
        ['code' => '423', 'name' => 'Letras por pagar', 'isrecord' => true],
    ]],

    // ✅ 50 Capital -> Patrimonio (raíz 5)
    ['code' => '50', 'name' => 'CAPITAL', 'root' => '5', 'children' => [
        ['code' => '501', 'name' => 'Capital social', 'isrecord' => true],
        ['code' => '502', 'name' => 'Aportes de socios', 'isrecord' => true],
    ]],

    // ✅ 59 Resultados acumulados -> Patrimonio (raíz 5)
    ['code' => '59', 'name' => 'RESULTADOS ACUMULADOS', 'root' => '5', 'children' => [
        ['code' => '591', 'name' => 'Utilidades no distribuidas', 'isrecord' => true],
        ['code' => '592', 'name' => 'Pérdidas acumuladas', 'isrecord' => true],
    ]],

    // 60 Compras -> Gastos (raíz 6)
    ['code' => '60', 'name' => 'COMPRAS', 'root' => '6', 'children' => [
        ['code' => '601', 'name' => 'Mercaderías', 'isrecord' => true],
        ['code' => '609', 'name' => 'Costos vinculados con las compras', 'children' => [
            ['code' => '6091', 'name' => 'Costos vinculados con compras de mercaderías', 'isrecord' => true],
        ]],
    ]],

    // 61 Variación -> Gastos (raíz 6)
    ['code' => '61', 'name' => 'VARIACIÓN DE EXISTENCIAS', 'root' => '6', 'children' => [
        ['code' => '611', 'name' => 'Mercaderías', 'isrecord' => true],
    ]],

    // 69 Costo de ventas -> Gastos (raíz 6)
    ['code' => '69', 'name' => 'COSTO DE VENTAS', 'root' => '6', 'children' => [
        ['code' => '691', 'name' => 'Mercaderías', 'isrecord' => true],
    ]],

    // 70 Ventas -> Ingresos (raíz 7)
    ['code' => '70', 'name' => 'VENTAS', 'root' => '7', 'children' => [
        ['code' => '701', 'name' => 'Mercaderías', 'children' => [
            ['code' => '7011', 'name' => 'Mercaderías', 'children' => [
                ['code' => '70111', 'name' => 'Terceros', 'isrecord' => true],
                ['code' => '70112', 'name' => 'Relacionadas', 'isrecord' => true],
            ]],
        ]],
        ['code' => '709', 'name' => 'Devoluciones sobre ventas', 'isrecord' => true],
    ]],

    // ✅ 75 Otros ingresos de gestión -> Ingresos (raíz 7)
    ['code' => '75', 'name' => 'OTROS INGRESOS DE GESTIÓN', 'root' => '7', 'children' => [
        ['code' => '751', 'name' => 'Ingresos por alquileres', 'isrecord' => true],
        ['code' => '759', 'name' => 'Otros ingresos de gestión', 'isrecord' => true],
    ]],

    // ✅ 94/95 Gastos por función -> Analítica (raíz 9)
    ['code' => '94', 'name' => 'GASTOS DE ADMINISTRACIÓN', 'root' => '9', 'children' => [
        ['code' => '941', 'name' => 'Gastos de personal', 'isrecord' => true],
        ['code' => '942', 'name' => 'Servicios de terceros', 'isrecord' => true],
        ['code' => '949', 'name' => 'Otros gastos de administración', 'isrecord' => true],
    ]],
    ['code' => '95', 'name' => 'GASTOS DE VENTAS', 'root' => '9', 'children' => [
        ['code' => '951', 'name' => 'Gastos de personal - ventas', 'isrecord' => true],
        ['code' => '952', 'name' => 'Publicidad y promoción', 'isrecord' => true],
        ['code' => '959', 'name' => 'Otros gastos de ventas', 'isrecord' => true],
    ]],
];



        // 4) Insertar cada árbol colgando del root correspondiente (0..9)
        foreach ($tree as $node) {
            $root = $this->rootAccountsByCode[$node['root']] ?? null;
            if (!$root) {
                throw new \RuntimeException("No existe cuenta raíz '{$node['root']}' para colgar {$node['code']}");
            }

            $this->upsertNode($node, $root);
        }
    }

    private function upsertNode(array $node, Account $parent): Account
    {
        $depth = (int) $parent->depth + 1;
        $path  = trim($parent->path, '/') . '/' . $node['code'];

        // Inferir tipo según raíz (toma el tipo del padre root)
        $accountTypeId = $parent->account_type_id;

        $account = Account::updateOrCreate(
            ['code' => $node['code']],
            [
                'parent_id'       => $parent->id,
                'name'            => $node['name'] ?? null,
                'tag'             => $node['tag'] ?? null,
                'equivalentcode'  => $node['equivalentcode'] ?? null,
                'account_type_id' => $node['account_type_id'] ?? $accountTypeId,
                'reconcile'       => (bool)($node['reconcile'] ?? false),
                'costcenter'      => (bool)($node['costcenter'] ?? false),
                'isrecord'        => (bool)($node['isrecord'] ?? false),
                'depth'           => $depth,
                'path'            => $path,
                'tax_id'          => $node['tax_id'] ?? null,
            ]
        );

        if (!empty($node['children']) && is_array($node['children'])) {
            foreach ($node['children'] as $child) {
                $this->upsertNode($child, $account);
            }
        }

        return $account;
    }
}

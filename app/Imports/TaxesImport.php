<?php

namespace App\Imports;

use App\Models\Tax;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Importa impuestos desde Excel/CSV.
 *
 * Patrón idéntico a JournalTypesImport — sin WithValidation ni SkipsOnError.
 *
 * Por qué se quitó WithValidation:
 *   La regla 'in:0,1' falla cuando Excel entrega los valores booleanos
 *   como float (0.0, 1.0) en lugar de int (0, 1). SkipsOnError descartaba
 *   todas las filas silenciosamente mostrando "éxito" con 0 registros.
 *   El casteo directo en model() es más robusto y tolerante.
 *
 * Estructura esperada del archivo:
 *   Fila 1  → Cabeceras: name | amount | amount_type | ...
 *   Fila 2+ → Datos reales
 */
class TaxesImport implements ToModel, WithHeadingRow
{
    /**
     * Convierte cada fila del Excel en un modelo Tax.
     * updateOrCreate permite re-importar sin duplicar registros.
     */
    public function model(array $row): ?Tax
    {
        // Ignora filas completamente vacías
        $name = trim((string) ($row['name'] ?? ''));
        if (empty($name)) return null;

        // Normaliza enums — tolera mayúsculas y espacios extra del Excel
        $amount_type  = strtolower(trim((string) ($row['amount_type']  ?? 'percent')));
        $type_tax_use = strtolower(trim((string) ($row['type_tax_use'] ?? 'sale')));

        // Fallback a valores válidos si el archivo trae datos fuera de rango
        if (!in_array($amount_type,  ['percent', 'fixed', 'division', 'group'])) {
            $amount_type = 'percent';
        }
        if (!in_array($type_tax_use, ['sale', 'purchase', 'none'])) {
            $type_tax_use = 'sale';
        }

        // Casteo explícito de booleanos:
        // Excel puede entregar 0/1 como int, float (0.0/1.0) o string ("0"/"1").
        // intval() normaliza cualquiera de esos formatos a 0 o 1 correctamente.
        return Tax::updateOrCreate(
            ['name' => $name],
            [
                'amount'              => (float)  ($row['amount']   ?? 0),
                'amount_type'         => $amount_type,
                'type_tax_use'        => $type_tax_use,
                'tax_scope'           => trim((string) ($row['tax_scope']  ?? '')) ?: null,
                'sequence'            => (int)    ($row['sequence'] ?? 1),
                'price_include'       => intval($row['price_include']       ?? 0) === 1,
                'include_base_amount' => intval($row['include_base_amount'] ?? 0) === 1,
                'is_base_affected'    => intval($row['is_base_affected']    ?? 0) === 1,
                'active'              => intval($row['active']              ?? 1) === 1,
                'description'         => trim((string) ($row['description'] ?? '')) ?: null,
            ]
        );
    }
}

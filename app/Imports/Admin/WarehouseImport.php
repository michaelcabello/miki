<?php

namespace App\Imports\Admin;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WarehouseImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Convierte cada fila del Excel en un Warehouse.
     * Usa updateOrCreate para tolerar re-importaciones sin duplicar.
     */
    public function model(array $row): ?Warehouse
    {
        // Normalizar código: mayúsculas, sin espacios, solo A-Z 0-9 _
        $code = strtoupper(trim((string) ($row['code'] ?? '')));
        $code = preg_replace('/\s+/', '_', $code);
        $code = preg_replace('/[^A-Z0-9_]/', '', $code);

        if (empty($code)) {
            return null;
        }

        return Warehouse::updateOrCreate(
            ['code' => $code],
            [
                'name'        => trim((string) ($row['name'] ?? '')),
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
                'address'     => trim((string) ($row['address'] ?? '')) ?: null,
                'is_main'     => (int) ($row['is_main'] ?? 0) === 1,
                'order'       => (int) ($row['order'] ?? 0),
                'state'       => (int) ($row['state'] ?? 1) === 1,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:10'],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'address'     => ['nullable', 'string', 'max:250'],
            'is_main'     => ['nullable', 'in:0,1'],
            'order'       => ['nullable', 'integer', 'min:0', 'max:9999'],
            'state'       => ['nullable', 'in:0,1'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'code.required'  => 'La columna "code" es obligatoria.',
            'code.max'       => 'El código no debe exceder 10 caracteres.',
            'name.required'  => 'La columna "name" es obligatoria.',
            'name.max'       => 'El nombre no debe exceder 150 caracteres.',
            'is_main.in'     => 'El campo is_main debe ser 0 o 1.',
            'order.integer'  => 'El orden debe ser un número entero.',
            'order.min'      => 'El orden no puede ser negativo.',
            'state.in'       => 'El estado debe ser 0 o 1.',
        ];
    }
}

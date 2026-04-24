<?php

namespace App\Imports\Admin;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WarehouseLocationImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Convierte cada fila del Excel en un WarehouseLocation.
     * Resolución de warehouse_id y parent_id por código para facilitar la importación.
     */
    public function model(array $row): ?WarehouseLocation
    {
        $code = strtoupper(trim((string) ($row['code'] ?? '')));
        $code = preg_replace('/[^A-Z0-9_\-\/]/', '', $code);

        if (empty($code)) {
            return null;
        }

        // Resolver warehouse_id desde código de almacén
        $warehouseId = null;
        if (!empty($row['warehouse_id'])) {
            $warehouse = Warehouse::where('code', strtoupper(trim($row['warehouse_id'])))
                ->orWhere('id', $row['warehouse_id'])
                ->first();
            $warehouseId = $warehouse?->id;
        }

        // Resolver parent_id desde código de ubicación padre
        $parentId = null;
        if (!empty($row['parent_id'])) {
            $parent = WarehouseLocation::where('code', strtoupper(trim($row['parent_id'])))
                ->orWhere('id', $row['parent_id'])
                ->first();
            $parentId = $parent?->id;
        }

        $usageValues = array_keys(WarehouseLocation::$usageLabels);
        $usage = in_array($row['usage'] ?? '', $usageValues) ? $row['usage'] : 'internal';

        $location = WarehouseLocation::updateOrCreate(
            ['code' => $code, 'warehouse_id' => $warehouseId],
            [
                'name'          => trim((string) ($row['name'] ?? '')),
                'order'         => (int) ($row['order'] ?? 0),
                'parent_id'     => $parentId,
                'warehouse_id'  => $warehouseId,
                'usage'         => $usage,
                'scrap_location' => (int) ($row['scrap_location'] ?? 0) === 1,
                'state'         => (int) ($row['state'] ?? 1) === 1,
                'capacity'      => !empty($row['capacity']) ? (float) $row['capacity'] : null,
            ]
        );

        // Regenerar complete_name
        $location->load(['parent', 'warehouse']);
        $location->complete_name = $location->generateCompleteName();
        $location->save();

        return $location;
    }

    public function rules(): array
    {
        return [
            'code'  => ['required', 'string', 'max:50'],
            'name'  => ['required', 'string', 'max:150'],
            'usage' => ['nullable', 'in:view,internal,supplier,customer,inventory,production,transit'],
            'scrap_location' => ['nullable', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'state' => ['nullable', 'in:0,1'],
            'capacity' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'code.required'  => 'La columna "code" es obligatoria.',
            'code.max'       => 'El código no debe exceder 50 caracteres.',
            'name.required'  => 'La columna "name" es obligatoria.',
            'usage.in'       => 'El campo usage debe ser: view, internal, supplier, customer, inventory, production o transit.',
            'state.in'       => 'El estado debe ser 0 o 1.',
        ];
    }
}

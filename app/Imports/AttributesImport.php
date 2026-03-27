<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Attribute;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

//php artisan make:import AttributesImport
class AttributesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
     * Convierte cada fila del Excel en un modelo Attribute.
     * Usa updateOrCreate para tolerar re-importaciones sin duplicar registros.
     */
    public function model(array $row): ?Attribute
    {
        // Normaliza el nombre: capitaliza primera letra de cada palabra
        $name = trim((string) ($row['name'] ?? ''));
        $name = preg_replace('/\s+/', ' ', $name);
        $name = mb_convert_case(mb_strtolower($name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');

        if (empty($name)) {
            return null;
        }

        // updateOrCreate: si ya existe el nombre lo actualiza, si no lo crea
        return Attribute::updateOrCreate(
            ['name' => $name],
            [
                'state' => (int) ($row['state'] ?? 1) === 1,
                'order' => (int) ($row['order'] ?? 0),
            ]
        );
    }

    /**
     * Reglas de validación por fila.
     * Se ejecutan antes de model() — filas inválidas se saltan con SkipsOnError.
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    /**
     * Mensajes de validación en español para las filas del Excel.
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'La columna "name" es obligatoria.',
            'name.max'      => 'El nombre no debe exceder 100 caracteres.',
            'state.in'      => 'El estado debe ser 0 o 1.',
            'order.integer' => 'El orden debe ser un número entero.',
            'order.min'     => 'El orden no puede ser negativo.',
            'order.max'     => 'El orden no puede exceder 65535.',
        ];
    }
}

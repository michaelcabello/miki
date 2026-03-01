<?php

namespace App\Imports;

use App\Models\JournalType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Throwable;

class JournalTypesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
     * Convierte cada fila del Excel en un modelo JournalType.
     * Usa updateOrCreate para tolerar re-importaciones sin duplicar registros.
     */
    public function model(array $row): ?JournalType
    {
        // Normaliza el código: mayúsculas, sin espacios, solo A-Z 0-9 _
        $code = strtoupper(trim((string) ($row['code'] ?? '')));
        $code = preg_replace('/\s+/', '_', $code);
        $code = preg_replace('/[^A-Z0-9_]/', '', $code);

        if (empty($code)) {
            return null;
        }

        // updateOrCreate: si ya existe el código lo actualiza, si no lo crea
        return JournalType::updateOrCreate(
            ['code' => $code],
            [
                'name'  => trim((string) ($row['name'] ?? '')),
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
            'code'  => ['required', 'string', 'max:30'],
            'name'  => ['required', 'string', 'max:120'],
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
            'code.required'  => 'La columna "code" es obligatoria.',
            'code.max'       => 'El código no debe exceder 30 caracteres.',
            'name.required'  => 'La columna "name" es obligatoria.',
            'name.max'       => 'El nombre no debe exceder 120 caracteres.',
            'state.in'       => 'El estado debe ser 0 o 1.',
            'order.integer'  => 'El orden debe ser un número entero.',
            'order.min'      => 'El orden no puede ser negativo.',
            'order.max'      => 'El orden no puede exceder 65535.',
        ];
    }
}

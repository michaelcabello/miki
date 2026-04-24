<?php

namespace App\Imports\Admin;

use App\Models\JournalType;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithMapping,
    SkipsEmptyRows,
    WithUpserts,
    WithBatchInserts,
    WithChunkReading
};

class JournalTypesImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithMapping,
    SkipsEmptyRows,
    WithUpserts,
    WithBatchInserts,
    WithChunkReading
{
    /**
     * 1. MAPEO Y LIMPIEZA (Pre-Validación)
     * Aquí transformamos los datos del Excel antes de validarlos.
     */
    public function prepareForValidation($data, $index)
    {
        // Normalizamos el código como un profesional
        $code = strtoupper(trim((string)($data['code'] ?? '')));
        $code = preg_replace('/[^A-Z0-9_]/', '', str_replace(' ', '_', $code));

        $data['code'] = $code;

        // Manejamos el estado: acepta "1", "Activo", "SI"
        $stateValue = strtolower(trim((string)($data['state'] ?? '1')));
        $data['state'] = in_array($stateValue, ['1', 'activo', 'si', 'true']) ? 1 : 0;

        return $data;
    }

    public function map($row): array
    {
        return [
            'code'  => $row['code'],
            'name'  => trim((string)$row['name']),
            'state' => (int)$row['state'],
            'order' => (int)($row['order'] ?? 0),
        ];
    }

    /**
     * 2. CREACIÓN DEL MODELO
     */

    public function modelFUNCIONA(array $row)
    {
        // 🚀 Usamos el operador ?? para evitar que el sistema explote si falta una columna
        // Y verificamos si la columna viene como 'code' o 'codigo'
        $codeValue = $row['code'] ?? $row['codigo'] ?? null;
        $nameValue = $row['name'] ?? $row['nombre'] ?? null;

        if (!$codeValue || !$nameValue) {
            throw new \Exception("Faltan columnas requeridas en el Excel (code/name).");
        }

        return new JournalType([
            'code'  => strtoupper(trim($codeValue)),
            'name'  => trim($nameValue),
            'state' => (int)($row['state'] ?? 1),
            'order' => (int)($row['order'] ?? 0),
        ]);
    }




    public function model(array $row)
    {
        // 🚀 Senior Tip: Si 'name' no existe como llave, es que faltan encabezados
        if (!isset($row['name']) && !isset($row['nombre'])) {
            throw new \Exception("El archivo Excel no tiene los encabezados correctos (code, name, state, order).");
        }

        return new JournalType([
            'code'  => strtoupper(trim($row['code'] ?? $row['codigo'])),
            'name'  => trim($row['name'] ?? $row['nombre']),
            'state' => in_array(strtolower($row['state'] ?? ''), ['activo', '1', 'si']) ? 1 : 0,
            'order' => (int)($row['order'] ?? 0),
        ]);
    }





    /**
     * 3. UPSERT (Update or Create a nivel de DB)
     * Define qué columna es la clave única para decidir si actualiza o crea.
     */
    public function uniqueBy()
    {
        return 'code';
    }

    /**
     * 4. REGLAS DE VALIDACIÓN ROBUSTAS
     */
    public function rules(): array
    {
        return [
            'code'  => ['required', 'string', 'max:30'],
            'name'  => ['required', 'string', 'max:120'],
            'state' => ['required', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'code.required' => 'El campo "code" es vital para identificar el registro.',
            'state.boolean' => 'El estado debe ser un valor lógico (1/0 o Activo/Inactivo).',
            'name.required'  => 'No podemos crear un tipo de diario sin nombre.',
        ];
    }

    /**
     * 5. OPTIMIZACIÓN DE RENDIMIENTO
     */
    public function batchSize(): int
    {
        return 500; // Inserta de 500 en 500 para mayor velocidad
    }

    public function chunkSize(): int
    {
        return 500; // Lee el archivo en trozos para no saturar la RAM
    }
}
